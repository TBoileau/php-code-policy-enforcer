<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Reflection;

use Stringable;

abstract readonly class ReflectionImport implements Stringable
{
    public function __construct(protected string $name, private ?string $alias = null)
    {
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function isNamespace(): bool;

    public function __toString(): string
    {
        return $this->name;
    }
}
