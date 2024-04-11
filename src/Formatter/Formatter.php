<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Formatter;

use TBoileau\PhpCodePolicyEnforcer\CodePolicy;

interface Formatter
{
    public function format(CodePolicy $codePolicy): void;
}
