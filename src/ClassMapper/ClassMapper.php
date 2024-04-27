<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\ClassMapper;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TBoileau\PhpCodePolicyEnforcer\Exception\ClassMapperException;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionImport;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionImportClass;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionImportFunction;

use function Symfony\Component\String\u;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Helpers\str_intersect;

final class ClassMapper
{
    /**
     * @var string[]
     */
    private array $directories = [];

    public function add(string $directory): self
    {
        if (!is_dir($directory)) {
            throw ClassMapperException::directoryDoesNotExist($directory);
        }

        $this->directories[] = $directory;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDirectories(): array
    {
        return array_map(
            static function (string $directory): string {
                /** @var string $cwd */
                $cwd = getcwd();

                /** @var string $directory */
                $directory = realpath($directory);

                $base = str_intersect($directory, $cwd);

                return u($directory)
                    ->replace($base, '')
                    ->padStart(1, '/')
                    ->ensureStart('/')
                    ->toString();
            },
            $this->directories
        );
    }

    public function generate(): ClassMap
    {
        $this->getDirectories();
        $finder = (new Finder())
            ->files()
            ->in(...$this->directories)
            ->name('*.php')
            ->contains('/(?m)^namespace\s+([^\s;]+);/')
            ->sortByName(true);

        $classMap = new ClassMap($this);

        foreach ($finder as $file) {
            [
                'fullQualifiedClassName' => $fullQualifiedClassName,
                'fileContents' => $fileContents
            ] = $this->getFullQualifiedClassName($file);

            if (
                !class_exists($fullQualifiedClassName)
                && !interface_exists($fullQualifiedClassName)
                && !trait_exists($fullQualifiedClassName)
                && !enum_exists($fullQualifiedClassName)
            ) {
                continue;
            }

            $header = $this->getHeader($fileContents);

            if ($header === null) {
                continue;
            }

            $imports = $this->getImports($header);

            $classMap->add(new ReflectionClass($fullQualifiedClassName, $imports));
        }

        return $classMap;
    }

    /**
     * @return array{namespace: string, fullQualifiedClassName: string, className: string, fileContents: string}
     * @throws ClassMapperException
     */
    private function getFullQualifiedClassName(SplFileInfo $file): array
    {
        $fileContents = $file->getContents();

        if (preg_match('/(?m)^namespace\s+(?<namespace>[^\s;]+);/', $fileContents, $matches) === 0) {
            throw ClassMapperException::fileIsNotClass($file);
        }

        $className = $file->getBasename('.php');

        return [
            'namespace' => $matches['namespace'],
            'className' => $className,
            'fullQualifiedClassName' => sprintf('%s\\%s', $matches['namespace'], $className),
            'fileContents' => $fileContents
        ];
    }

    private function getHeader(string $fileContents): ?string
    {
        if (preg_match('/(?<header>.+)(?:class|interface|enum|trait)/s', $fileContents, $matches) === 0) {
            return null;
        }

        return $matches['header'];
    }

    /**
     * @return ReflectionImport[]
     */
    private function getImports(string $header): array
    {
        if (preg_match_all('/use(?:\sfunction)?\s+(?<imports>[^;]+);/', $header, $matches) === 0 || count($matches['imports']) === 0) {
            return [];
        }

        /** @var ReflectionImport[] $imports */
        $imports = [];

        foreach ($matches['imports'] as $import) {
            if (!is_string($import)) {
                continue;
            }

            $import = $this->sanitizeImport($import);

            if (null === $import) {
                continue;
            }

            $isFunction = str_contains($import, 'function');

            $import = trim(str_replace(array("use function", "use"), '', $import));

            if (preg_match('/^[^{}]*$/', $import, $matches) !== 0) {
                $imports[] = $this->getImport($import, $isFunction);
                continue;
            }

            if (preg_match('/^([^{}]*)(?:\{([^{}]*)\})?$/', $import, $matches) > 0) {
                $namespace = $matches[1];
                $childClasses = explode(',', $matches[2]);

                foreach ($childClasses as $childClass) {
                    $imports[] = $this->getImport($childClass, $isFunction, $namespace);
                }
            }
        }

        return $imports;
    }

    private function sanitizeImport(string $import): ?string
    {
        return preg_replace(
            '/\s+/',
            ' ',
            str_replace(
                array("\r", "\n", ';'),
                '',
                str_replace(
                    array(' {', '{ ', ' }', '} '),
                    array('{', '{', '}', '}'),
                    $import
                )
            )
        );
    }

    private function getImport(string $import, bool $isFunction, string $namespace = ''): ReflectionImport
    {
        $classNameAndAlias = explode(' as ', $import);

        $fullQualifiedClassName = $namespace.trim($classNameAndAlias[0]);

        return match ($isFunction) {
            true => new ReflectionImportFunction($fullQualifiedClassName, $classNameAndAlias[1] ?? null),
            false => new ReflectionImportClass($fullQualifiedClassName, $classNameAndAlias[1] ?? null),
        };
    }
}
