<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use ReflectionClass as InternalReflectionClass;
use ReflectionException;

final class ReflectionClass extends InternalReflectionClass
{
    /**
     * @param object|class-string $objectOrClass
     * @param ReflectionImport[] $imports,
     * @throws ReflectionException
     */
    public function __construct(
        object|string $objectOrClass,
        private readonly array $imports = []
    ) {
        parent::__construct($objectOrClass);
    }

    /**
     * @return ReflectionImport[]
     */
    public function getImports(): array
    {
        return $this->imports;
    }
}
