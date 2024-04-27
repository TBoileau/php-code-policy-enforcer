<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Exception;

use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Report\ValueReport;

final class ReportException extends CodePolicyEnforcerException
{
    public static function valueCannotBeEvaluated(ValueReport $valueReport): self
    {
        return new self(
            'The value cannot be evaluated because it state is "ignored".',
            ['valueReport' => $valueReport]
        );
    }
}
