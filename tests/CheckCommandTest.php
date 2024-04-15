<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use TBoileau\PhpCodePolicyEnforcer\Cli\Application;

final class CheckCommandTest extends TestCase
{
    #[Test]
    public function shouldRunSuccessfully(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);

        $applicationTester->run([
            'check',
            '--config' => __DIR__ . '/../tools/php-code-policy-enforcer.php'
        ]);

        $applicationTester->assertCommandIsSuccessful();
    }
}
