<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use LogicException;
use TBoileau\PhpCodePolicyEnforcer\Exception\RuleException;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Type;
use TBoileau\PhpCodePolicyEnforcer\Templating\Templating;

use function Symfony\Component\String\u;

final class Rule
{
    private ?LogicalExpression $filter = null;

    private ?LogicalExpression $checker = null;

    private ?string $reason = null;

    private ?CodePolicy $codePolicy = null;

    public function getCodePolicy(): CodePolicy
    {
        if (null === $this->codePolicy) {
            throw RuleException::codePolicyNotProvide($this);
        }

        return $this->codePolicy;
    }

    public function setCodePolicy(CodePolicy $codePolicy): self
    {
        $this->codePolicy = $codePolicy;

        return $this;
    }

    public function that(Expression ...$expressions): Rule
    {
        $this->filter = new LogicalExpression();

        foreach ($expressions as $expression) {
            $this->filter->add($expression);
        }

        return $this;
    }

    public function should(Expression ...$expressions): Rule
    {
        $this->checker = new LogicalExpression();

        foreach ($expressions as $expression) {
            $this->checker->add($expression);
        }

        return $this;
    }

    public function because(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReason(): string
    {
        if (null === $this->reason) {
            throw RuleException::reasonNotProvide($this);
        }

        return $this->reason;
    }

    public function getFilter(): LogicalExpression
    {
        if (null === $this->filter) {
            throw RuleException::filterNotProvide($this);
        }

        return $this->filter;
    }

    public function getChecker(): LogicalExpression
    {
        if (null === $this->checker) {
            throw RuleException::checkerNotProvide($this);
        }

        return $this->checker;
    }
}
