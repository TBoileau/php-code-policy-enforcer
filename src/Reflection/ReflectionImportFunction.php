<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use ReflectionFunction;

final readonly class ReflectionImportFunction
{
    public function __construct(private string $function, private ?string $alias = null)
    {
    }

    public function getFunction(): ReflectionFunction
    {
        return new ReflectionFunction($this->function);
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
