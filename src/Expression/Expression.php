<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use Closure;

interface Expression
{
    /**
     * @param ?Closure(Expression, bool, mixed): void $on
     */
    public function evaluate(mixed $value, ?Closure $on = null): bool;

    public function attachTo(LogicalExpression $parent): Expression;

    public function parent(): ?LogicalExpression;
}
