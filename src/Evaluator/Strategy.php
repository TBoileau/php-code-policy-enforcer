<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Evaluator;

use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;

interface Strategy
{
    public function evaluate(LogicalExpression $expression, mixed $value): bool;
}
