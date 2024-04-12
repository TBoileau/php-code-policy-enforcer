<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use LogicException;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;

final class ValueReport
{
    private ?Report $that = null;

    private ?Report $should = null;

    public function __construct(
        private readonly RuleReport $ruleReport,
        private readonly mixed $value,
    ) {
        $this->setOnEvaluate($this->that, $this->ruleReport->rule()->getThat());
        $this->setOnEvaluate($this->should, $this->ruleReport->rule()->getShould());
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function that(): Report
    {
        if ($this->that === null) {
            throw new LogicException('You cannot retrieve the filter (that) report before setting up by calling "filter" method.');
        }

        return $this->that;
    }

    public function should(): Report
    {
        if (!$this->that()->has(Status::Succeeded)) {
            throw new LogicException('You cannot retrieve the valuation report because this value has been ignored.');
        }

        if ($this->should === null) {
            throw new LogicException('You cannot retrieve the evaluation (should) report before setting up by calling "evaluate" method.');
        }

        return $this->should;
    }

    public function is(State $state): bool
    {
        return $state->equals($this->state());
    }

    public function has(Status $status): bool
    {
        return $status->equals($this->status());
    }

    public function state(): State
    {
        return $this->that()->has(Status::Succeeded) ? State::Evaluated : State::Ignored;
    }

    public function status(): Status
    {
        if ($this->that()->has(Status::Succeeded)) {
            return $this->should()->status();
        }

        throw new LogicException('You cannot retrieve the report status because this value has been ignored.');
    }

    private function setOnEvaluate(?Report &$report, Expression $expression): void
    {
        $tempReports = [];

        $expression->onEvaluate(function (bool $result) use (&$tempReports, &$report): void {
            /** @var Expression $this */
            $parent = new Report($this, Status::fromResult($result));

            if ($this instanceof ConditionalExpression) {
                $tempReports[] = $parent;
                return;
            }

            foreach ($tempReports as $tempReport) {
                $parent->add($tempReport);
            }

            if ($this->isRoot()) {
                $report = $parent;
                return;
            }

            $tempReports = [$parent];
        });
    }
}
