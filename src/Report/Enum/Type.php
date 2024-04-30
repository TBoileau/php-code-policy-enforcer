<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report\Enum;

use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;

enum Type: string
{
    case Conditional = ConditionalExpression::class;
    case Logical = LogicalExpression::class;

    public function equals(Expression $expression): bool
    {
        return $expression instanceof $this->value;
    }
}
