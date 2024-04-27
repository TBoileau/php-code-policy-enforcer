<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator;

use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;

interface Strategy
{
    public function evaluate(LogicalExpression $expression, Report $report): bool;
}
