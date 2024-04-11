<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\ClassMapper;

use ReflectionException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class ClassMapper
{
    /**
     * @param string $directory
     * @return ClassMap
     * @throws ReflectionException
     */
    public static function generateClassMap(string $directory): ClassMap
    {
        ['executablePath' => $executablePath, 'outputFile' => $outputFile] = self::determineExecutableName();

        $process = new Process([$executablePath, '--dir', $directory , '--out', $outputFile]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        /** @var array<string> $classes */
        $classes = require $outputFile;

        unlink($outputFile);

        /** @var array<class-string> $classes */
        $classes = array_filter(
            $classes,
            static fn (string $class): bool => class_exists($class) || interface_exists($class) || trait_exists($class)
        );

        sort($classes);

        return ClassMap::fromArrayOfFqcn($classes);
    }

    /**
     * @return array{executablePath: string, outputFile: string}
     */
    private static function determineExecutableName(): array
    {
        $osPart = match ($os = strtolower(PHP_OS)) {
            'win' => 'windows',
            default => $os,
        };

        $archPart = match ($arch = strtolower(php_uname('m'))) {
            'arm64' => 'arm64',
            default => 'amd64',
        };

        $executableName = sprintf('class_mapper-%s-%s', $osPart, $archPart);
        $executablePath = __DIR__ . '/../../bin/' . $executableName;

        $outputFile = sys_get_temp_dir() . '/' . uniqid('class_map-', true) . '.php';

        if (!file_exists($executablePath)) {
            throw new RuntimeException(sprintf('Executable not found for your platform (%s/%s): %s', $os, $arch, $executablePath));
        }

        return compact('executablePath', 'outputFile');
    }
}
