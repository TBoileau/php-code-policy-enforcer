<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use Closure;

final class Condition implements Expression
{
    private LogicalExpression $parent;

    /**
     * @param Closure(mixed): bool $validator
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly string  $name,
        private readonly Closure $validator,
        private readonly array   $parameters = [],
        private readonly string  $message = ''
    ) {
    }

    public function attachTo(LogicalExpression $parent): Expression
    {
        $this->parent = $parent;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function evaluate(mixed $value, ?Closure $on = null): bool
    {
        $result = ($this->validator)($value);

        if (null !== $on) {
            $on($this, $result, $value);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function parent(): LogicalExpression
    {
        return $this->parent;
    }

    public function message(): string
    {
        return $this->message;
    }
}
