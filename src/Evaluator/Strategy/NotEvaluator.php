<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;

final readonly class NotEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, mixed $value): bool
    {
        return !$expression[0]->evaluate($value);
    }
}
