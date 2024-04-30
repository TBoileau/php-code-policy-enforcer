<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use TBoileau\PhpCodePolicyEnforcer\Report\Report;

interface Expression
{
    public function evaluate(Report $report): bool;

    public function setParent(Expression $parent): Expression;

    public function getParent(): ?Expression;

    public function getLevel(): int;

    public function isChildExpression(): bool;
}
