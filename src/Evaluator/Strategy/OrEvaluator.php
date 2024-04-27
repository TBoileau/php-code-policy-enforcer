<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;

use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;

final readonly class OrEvaluator implements Strategy
{
    public function evaluate(LogicalExpression $expression, Report $report): bool
    {
        $result = false;

        foreach ($expression as $child) {
            $childReport = new Report($report->value(), $child);

            if ($child->evaluate($childReport)) {
                $result = true;
            }

            $report->add($childReport);
        }

        $report->setStatus(Status::fromResult($result));

        return $result;
    }
}
