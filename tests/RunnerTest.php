<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\RuleReport;
use TBoileau\PhpCodePolicyEnforcer\Report\RuleSetReport;
use TBoileau\PhpCodePolicyEnforcer\Report\ValueReport;
use TBoileau\PhpCodePolicyEnforcer\RuleSet;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Rule;
use TBoileau\PhpCodePolicyEnforcer\Runner;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Bar;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Baz;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Corge;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Foo;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Quux;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Qux;

use function TBoileau\PhpCodePolicyEnforcer\Lib\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\hasProperty;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isSubclassOf;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\orX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\xorX;

final class RunnerTest extends TestCase
{
    #[Test]
    public function shouldRunSuccessfully(): void
    {
        $codePolicy = (new CodePolicy())
            ->add(
                RuleSet::scan(__DIR__)
                    ->add(
                        Rule::classes()
                            ->that(residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'))
                            ->should(
                                orX(
                                    isEnum(),
                                    not(isInterface()),
                                    xorX(
                                        isFinal(),
                                        not(isAbstract())
                                    ),
                                    not(
                                        andX(
                                            hasMethod('__invoke'),
                                            hasProperty('bar')
                                        )
                                    )
                                ),
                                matchWith('/.+Controller$/')
                            )
                            ->because('rule 1')
                    )
                ->add(
                    Rule::classes()
                        ->that(residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'))
                        ->should(not(isSubclassOf(TestCase::class)))
                        ->because('rule 2')
                )
            )
            ->add(
                RuleSet::scan(__DIR__ . '/')
                    ->add(
                        Rule::classes()
                            ->that(isSubclassOf(TestCase::class))
                            ->should(
                                isFinal(),
                                matchWith('/.+Test$/')
                            )
                            ->because('rule 3')
                    )
            );

        $runner = new Runner($codePolicy);

        $report = $runner->run();

        self::assertCount(3, $report);

        $ruleSet1Report = $report[0];
        self::assertInstanceOf(RuleSetReport::class, $ruleSet1Report);
        self::assertCount(2, $ruleSet1Report);

        self::assertRuleReport(
            10,
            Status::Failed,
            [
                [CheckCommandTest::class, State::Ignored, null],
                [ClassMapperTest::class, State::Ignored, null],
                [CompilerTest::class, State::Ignored, null],
                [Bar::class, State::Evaluated, Status::Failed],
                [Baz::class, State::Evaluated, Status::Failed],
                [Corge::class, State::Evaluated, Status::Failed],
                [Foo::class, State::Evaluated, Status::Failed],
                [Quux::class, State::Evaluated, Status::Failed],
                [Qux::class, State::Evaluated, Status::Failed],
                [RunnerTest::class, State::Ignored, null]
            ],
            $ruleSet1Report[0]
        );

        self::assertRuleReport(
            10,
            Status::Succeeded,
            [
                [CheckCommandTest::class, State::Ignored, null],
                [ClassMapperTest::class, State::Ignored, null],
                [CompilerTest::class, State::Ignored, null],
                [Bar::class, State::Evaluated, Status::Succeeded],
                [Baz::class, State::Evaluated, Status::Succeeded],
                [Corge::class, State::Evaluated, Status::Succeeded],
                [Foo::class, State::Evaluated, Status::Succeeded],
                [Quux::class, State::Evaluated, Status::Succeeded],
                [Qux::class, State::Evaluated, Status::Succeeded],
                [RunnerTest::class, State::Ignored, null]
            ],
            $ruleSet1Report[1]
        );

        $ruleSet2Report = $report[1];
        self::assertInstanceOf(RuleSetReport::class, $ruleSet2Report);
        self::assertCount(1, $ruleSet2Report);
        self::assertContainsOnlyInstancesOf(RuleReport::class, $ruleSet2Report);

        self::assertRuleReport(
            10,
            Status::Succeeded,
            [
                [CheckCommandTest::class, State::Evaluated, Status::Succeeded],
                [ClassMapperTest::class, State::Evaluated, Status::Succeeded],
                [CompilerTest::class, State::Evaluated, Status::Succeeded],
                [Bar::class, State::Ignored, null],
                [Baz::class, State::Ignored, null],
                [Corge::class, State::Ignored, null],
                [Foo::class, State::Ignored, null],
                [Quux::class, State::Ignored, null],
                [Qux::class, State::Ignored, null],
                [RunnerTest::class, State::Evaluated, Status::Succeeded]
            ],
            $ruleSet2Report[0]
        );
    }

    /**
     * @param array{class-string, State, ?Status}[] $expectedValueReports
     */
    private static function assertRuleReport(
        int $expectedCount,
        Status $expectedStatus,
        array $expectedValueReports,
        mixed $report
    ): void {
        self::assertInstanceOf(RuleReport::class, $report);
        self::assertCount($expectedCount, $report);
        self::assertTrue($report->has($expectedStatus));

        foreach ($expectedValueReports as $i => [$value, $state, $status]) {
            self::assertValueReport($value, $state, $status, $report[$i]);
        }
    }

    /**
     * @param class-string $expectedValue
     */
    private static function assertValueReport(string $expectedValue, State $expectedState, ?Status $expectedStatus, mixed $report): void
    {
        self::assertInstanceOf(ValueReport::class, $report);
        $value = $report->value();
        self::assertInstanceOf(ReflectionClass::class, $value);
        self::assertSame($expectedValue, $value->getName());
        self::assertTrue($report->is($expectedState));

        if ($expectedStatus !== null) {
            self::assertTrue($report->has($expectedStatus));
        }
    }
}
