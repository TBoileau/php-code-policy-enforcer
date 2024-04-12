<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;

final readonly class XorEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, mixed $value): bool
    {
        $count = 0;
        foreach ($expression as $child) {
            if ($child->evaluate($value)) {
                $count++;
            }
        }

        return 1 === $count;
    }
}
