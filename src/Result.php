<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;

final class Result
{
    /**
     * @var Result[]
     */
    private array $children = [];

    public function __construct(private readonly Expression $expression, private readonly bool $result, private readonly mixed $value)
    {
    }

    /**
     * @return Result[]
     */
    public function children(): array
    {
        return $this->children;
    }

    public function add(Result $result): void
    {
        $this->children[] = $result;
    }

    public function expression(): Expression
    {
        return $this->expression;
    }

    public function result(): bool
    {
        return $this->result;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
