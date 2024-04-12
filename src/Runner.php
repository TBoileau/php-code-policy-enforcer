<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Report\RunReport;

final class Runner
{
    private readonly RunReport $report;

    private ?Closure $onHit = null;

    public function __construct(private readonly CodePolicy $codePolicy)
    {
        $this->report = new RunReport($this->codePolicy);
    }

    public function onHit(Closure $onHit): self
    {
        $this->onHit = $onHit;

        return $this;
    }

    public function run(): RunReport
    {
        foreach ($this->codePolicy as $ruleSet) {
            $ruleSetReport = $this->report->add($ruleSet);
            foreach ($ruleSet as $rule) {
                $rule->check($ruleSetReport, $this->onHit);
            }
        }

        return $this->report;
    }
}
