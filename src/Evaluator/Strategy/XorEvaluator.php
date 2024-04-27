<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;

final readonly class XorEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, Report $report): bool
    {
        $count = 0;
        foreach ($expression as $child) {
            $childReport = new Report($report->value(), $child);

            if ($child->evaluate($childReport)) {
                $count++;
            }

            $report->add($childReport);
        }

        $result = 1 === $count;

        $report->setStatus(Status::fromResult($result));

        return $result;
    }
}
