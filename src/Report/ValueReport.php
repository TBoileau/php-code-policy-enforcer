<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Exception\ReportException;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;

final class ValueReport
{
    private Report $that;

    private Report $should;

    public function __construct(
        private readonly RuleReport $ruleReport,
        private readonly ReflectionClass      $value,
        private readonly ?Closure   $onHit
    ) {
    }

    public function value(): ReflectionClass
    {
        return $this->value;
    }

    public function that(): Report
    {
        return $this->that;
    }

    public function should(): Report
    {
        if (!$this->that()->has(Status::Succeeded)) {
            throw ReportException::valueCannotBeEvaluated($this);
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

        throw ReportException::valueCannotBeEvaluated($this);
    }

    public function run(): void
    {
        $this->that = new Report($this->value, $this->ruleReport->rule()->getFilter());

        $this->ruleReport->rule()->getFilter()->evaluate($this->that);

        if ($this->onHit !== null) {
            ($this->onHit)($this);
        }
        if ($this->state()->equals(State::Ignored)) {
            return;
        }

        $this->should = new Report($this->value, $this->ruleReport->rule()->getChecker());

        $this->ruleReport->rule()->getChecker()->evaluate($this->should);
    }
}
