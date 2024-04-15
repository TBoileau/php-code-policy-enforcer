<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use ReflectionClass as InternalReflectionClass;

final readonly class ReflectionImportClass extends ReflectionImport
{
    public function getClass(): InternalReflectionClass
    {
        return new InternalReflectionClass($this->name);
    }

    public function isNamespace(): bool
    {
        return !class_exists($this->name);
    }
}
