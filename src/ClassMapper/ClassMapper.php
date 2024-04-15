<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\ClassMapper;

use ReflectionException;
use Symfony\Component\Finder\Finder;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionImport;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionImportClass;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionImportFunction;

final readonly class ClassMapper
{
    /**
     * @throws ReflectionException
     */
    public static function generateClassMap(string ...$directories): ClassMap
    {
        $finder = (new Finder())
            ->files()
            ->in(...$directories)
            ->name('*.php')
            ->contains('/(?m)^namespace\s+([^\s;]+);/')
            ->sortByName(true);

        /** @var ReflectionClass[] $classes */
        $classes = [];

        foreach ($finder as $file) {
            $contents = $file->getContents();

            if (preg_match('/(?m)^namespace\s+(?<namespace>[^\s;]+);/', $contents, $matches) === 0) {
                continue;
            }

            $namespace = $matches['namespace'];
            $className = $file->getBasename('.php');
            $fqcn = sprintf('%s\\%s', $namespace, $file->getBasename('.php'));

            if (!class_exists($fqcn) && !interface_exists($fqcn) && !trait_exists($fqcn) && !enum_exists($fqcn)) {
                continue;
            }

            if (preg_match(sprintf('/(.+)(?:class|interface|enum|trait)[ ]+%s/s', $className), $contents, $matches) === 0) {
                continue;
            }

            $contents = $matches[0];

            if (preg_match_all('/use(?:\sfunction)?\s+([^;]+);/', $contents, $matches) === 0 || count($matches[0]) === 0) {
                $classes[$fqcn] = new ReflectionClass($fqcn, []);
                continue;
            }

            /** @var ReflectionImport[] $imports */
            $imports = [];

            foreach ($matches[0] as $import) {
                if (!is_string($import)) {
                    continue;
                }

                $import = preg_replace('/\s+/', ' ', str_replace(array("\r", "\n", ';'), '', str_replace(array(' {', '{ ', ' }', '} '), array('{', '{', '}', '}'), $import)));

                if (null === $import) {
                    continue;
                }

                $type = str_contains($import, 'function') ? 'function' : 'class';

                $import = trim(str_replace(array("use function", "use"), '', $import));

                if (preg_match('/^[^{}]*$/', $import, $matches) !== 0) {
                    $subClass = explode(' as ', $import);

                    $alias = !isset($subClass[1]) ? null : trim($subClass[1]);
                    $subFqcn = trim($subClass[0]);

                    $imports[] = match ($type) {
                        'function' => new ReflectionImportFunction($subFqcn, $alias),
                        'class' => new ReflectionImportClass($subFqcn, $alias),
                    };
                    continue;
                }

                if (preg_match('/^([^{}]*)(?:\{([^{}]*)\})?$/', $import, $matches) !== 0) {
                    $namespace = $matches[1];
                    $subClasses = explode(',', $matches[2]);

                    foreach ($subClasses as $subClassName) {
                        $subClass = explode(' as ', $subClassName);
                        $subFqcn = $namespace.trim($subClass[0]);
                        $alias = !isset($subClass[1]) ? null : trim($subClass[1]);

                        $imports[] = match ($type) {
                            'function' => new ReflectionImportFunction($subFqcn, $alias),
                            'class' => new ReflectionImportClass($subFqcn, $alias),
                        };
                    }
                }
            }

            $classes[$fqcn] = new ReflectionClass($fqcn, $imports);
        }

        return new ClassMap($classes);
    }
}
