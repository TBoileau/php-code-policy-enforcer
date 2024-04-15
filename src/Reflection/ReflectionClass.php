<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use ReflectionClass as InternalReflectionClass;
use ReflectionException;

final class ReflectionClass extends InternalReflectionClass
{
    /**
     * @param object|class-string $objectOrClass
     * @param ReflectionImportClass[] $importedClasses,
     * @param ReflectionImportFunction[] $importedFunction,
     * @throws ReflectionException
     */
    public function __construct(
        object|string $objectOrClass,
        private readonly array $importedClasses = [],
        private readonly array $importedFunction = []
    ) {
        parent::__construct($objectOrClass);
    }

    /**
     * @return ReflectionImportClass[]
     */
    public function getImportedClasses(): array
    {
        return $this->importedClasses;
    }

    /**
     * @return ReflectionImportFunction[]
     */
    public function getImportedFunctions(): array
    {
        return $this->importedFunction;
    }
}
