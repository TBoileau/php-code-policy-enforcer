<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;

final readonly class AndEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, Report $report): bool
    {
        $result = true;

        foreach ($expression as $child) {
            $childReport = new Report($report->value(), $child);

            if (!$child->evaluate($childReport)) {
                $result = false;
            }

            $report->add($childReport);
        }

        $report->setStatus(Status::fromResult($result));

        return $result;
    }
}
