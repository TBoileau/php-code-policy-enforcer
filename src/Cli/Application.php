<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Cli;

use Symfony\Component\Console\Application as BaseApplication;
use TBoileau\PhpCodePolicyEnforcer\Cli\Command\CheckCommand;

final class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('PHP Code Policy Enforcer', '0.2.0');
        $this->add(new CheckCommand());
    }
}
