<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;

final readonly class OrEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, mixed $value): bool
    {
        $result = false;

        foreach ($expression as $child) {
            if (!$child->evaluate($value)) {
                $result = true;
            }
        }

        return $result;
    }
}
