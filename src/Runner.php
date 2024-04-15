<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Report\RunReport;

final class Runner
{
    private ?Closure $onHit = null;

    public function __construct(private readonly CodePolicy $codePolicy)
    {
    }

    public function onHit(Closure $onHit): self
    {
        $this->onHit = $onHit;

        return $this;
    }

    public function run(): RunReport
    {
        $runReport = new RunReport($this->codePolicy);

        foreach ($this->codePolicy as $rule) {
            $runReport->add($rule->check($this->codePolicy->classMap(), $this->onHit));
        }

        return $runReport;
    }
}
