<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Formatter;

use TBoileau\PhpCodePolicyEnforcer\Report\RunReport;

interface Formatter
{
    public function format(RunReport $report): void;
}
