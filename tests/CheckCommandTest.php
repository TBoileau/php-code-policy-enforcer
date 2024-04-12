<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TBoileau\PhpCodePolicyEnforcer\Command\CheckCommand;

final class CheckCommandTest extends TestCase
{
    #[Test]
    public function shouldRunSuccessfully(): void
    {
        $commandTester = new CommandTester(new CheckCommand());
        $commandTester->execute([
             '--config' => __DIR__ . '/../tools/php-code-policy-enforcer.php'
        ]);
        $commandTester->assertCommandIsSuccessful();
    }
}
