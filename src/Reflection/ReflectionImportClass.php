<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use ReflectionClass as InternalReflectionClass;

final readonly class ReflectionImportClass
{
    public function __construct(private string $class, private ?string $alias = null)
    {
    }

    public function getClass(): InternalReflectionClass
    {
        return new InternalReflectionClass($this->class);
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
