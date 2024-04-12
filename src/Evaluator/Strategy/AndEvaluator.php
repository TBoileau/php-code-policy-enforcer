<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;

final readonly class AndEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, mixed $value): bool
    {
        $result = true;

        foreach ($expression as $child) {
            if (!$child->evaluate($value)) {
                $result = false;
            }
        }

        return $result;
    }
}
