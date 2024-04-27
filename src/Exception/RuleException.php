<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Exception;

use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Report\ValueReport;
use TBoileau\PhpCodePolicyEnforcer\Rule;

final class RuleException extends CodePolicyEnforcerException
{
    public static function reasonNotProvide(Rule $rule): self
    {
        return new self(
            'You must provide a reason by calling the method named "because".',
            ['rule' => $rule]
        );
    }

    public static function filterNotProvide(Rule $rule): self
    {
        return new self(
            'You must provide a filter by calling the method or function named "that".',
            ['rule' => $rule]
        );
    }

    public static function checkerNotProvide(Rule $rule): self
    {
        return new self(
            'You must provide a checker by calling the method named "should".',
            ['rule' => $rule]
        );
    }

    public static function codePolicyNotProvide(Rule $rule): self
    {
        return new self(
            'You must provide a codePolicy by calling the method named "setCodePolicy".',
            ['rule' => $rule]
        );
    }
}
