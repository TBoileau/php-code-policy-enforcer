<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Cli;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use TBoileau\PhpCodePolicyEnforcer\Cli\Application;

final class ApplicationTest extends TestCase
{
    #[Test]
    public function shouldRunSuccessfully(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);

        $applicationTester->run([
            'check',
            '--config' => __DIR__ . '/../Fixtures/php-code-policy-enforcer.php'
        ]);

        $applicationTester->assertCommandIsSuccessful();
    }
}
