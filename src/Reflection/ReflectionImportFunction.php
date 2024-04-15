<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use ReflectionFunction;

final readonly class ReflectionImportFunction extends ReflectionImport
{
    public function getFunction(): ReflectionFunction
    {
        return new ReflectionFunction($this->name);
    }

    public function isNamespace(): bool
    {
        return !function_exists($this->name);
    }
}
