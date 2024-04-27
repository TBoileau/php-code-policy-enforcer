<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Exception;

use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;

final class ExpressionException extends CodePolicyEnforcerException
{
    public static function noChildValues(ConditionalExpression $expression): self
    {
        return new self(
            sprintf('"%s" validator has no child values set.', $expression->getName()),
            ['expression' => $expression]
        );
    }
}
