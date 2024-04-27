<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\State;
use TBoileau\PhpCodePolicyEnforcer\Report\Enum\Status;
use TBoileau\PhpCodePolicyEnforcer\Report\RuleReport;
use TBoileau\PhpCodePolicyEnforcer\Report\ValueReport;
use TBoileau\PhpCodePolicyEnforcer\Runner;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Bar;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Baz;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Corge;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Foo;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault\Garply;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault\Qux;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Quux;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Xyzzy;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Helpers\that;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\orX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\xorX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasProperty;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isSubclassOf;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isTrait;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\methods;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\containsParameters;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasReturnType;

final class RunnerTest extends TestCase
{
    #[Test]
    public function shouldRunSuccessfully(): void
    {
        $codePolicy = CodePolicy::in(__DIR__ . '/Fixtures')
            ->add(
                that(residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'))
                    ->should(
                        orX(
                            isEnum(),
                            not(isInterface()),
                            xorX(
                                isFinal(),
                                not(isAbstract())
                            ),
                            orX(
                                not(hasMethod('__invoke')),
                                not(hasProperty('bar'))
                            )
                        ),
                        matchWith('/.+Controller$/')
                    )
                    ->because('rule 1')
            )
            ->add(
                that(residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'))
                    ->should(not(isSubclassOf(TestCase::class)))
                    ->because('rule 2')
            )
            ->add(
                that(isSubclassOf(TestCase::class))
                    ->should(
                        isFinal(),
                        matchWith('/.+Test$/')
                    )
                    ->because('rule 3')
            )
            ->add(
                that(
                    residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'),
                    matchWith('/Foo/')
                )
                    ->should(
                        hasMethod(
                            '__invoke',
                            andX(
                                containsParameters(1),
                                hasReturnType()
                            )
                        )
                    )
                    ->because('rule 4')
            )
            ->add(
                that(
                    residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'),
                    andX(
                        not(isEnum()),
                        not(isInterface()),
                        not(isTrait())
                    )
                )
                    ->should(
                        methods(
                            andX(
                                containsParameters(1),
                                hasReturnType()
                            )
                        )
                    )
                    ->because('rule 5')
            );

        $runner = new Runner($codePolicy);

        $report = $runner->run();

        self::assertCount(40, $report);

        self::assertRuleReport(
            Status::Failed,
            [
                [Bar::class, State::Evaluated, Status::Failed],
                [Baz::class, State::Evaluated, Status::Failed],
                [Corge::class, State::Evaluated, Status::Failed],
                [Foo::class, State::Evaluated, Status::Failed],
                [Garply::class, State::Evaluated, Status::Failed],
                [Qux::class, State::Evaluated, Status::Failed],
                [Quux::class, State::Evaluated, Status::Failed],
                [Xyzzy::class, State::Evaluated, Status::Failed],
            ],
            $report[0],
            'Rule #0'
        );

        self::assertRuleReport(
            Status::Succeeded,
            [
                [Bar::class, State::Evaluated, Status::Succeeded],
                [Baz::class, State::Evaluated, Status::Succeeded],
                [Corge::class, State::Evaluated, Status::Succeeded],
                [Foo::class, State::Evaluated, Status::Succeeded],
                [Garply::class, State::Evaluated, Status::Succeeded],
                [Qux::class, State::Evaluated, Status::Succeeded],
                [Quux::class, State::Evaluated, Status::Succeeded],
                [Xyzzy::class, State::Evaluated, Status::Succeeded],
            ],
            $report[1],
            'Rule #1'
        );

        self::assertRuleReport(
            Status::Succeeded,
            [
                [Bar::class, State::Ignored, null],
                [Baz::class, State::Ignored, null],
                [Corge::class, State::Ignored, null],
                [Foo::class, State::Ignored, null],
                [Garply::class, State::Ignored, null],
                [Qux::class, State::Ignored, null],
                [Quux::class, State::Ignored, null],
                [Xyzzy::class, State::Ignored, null],
            ],
            $report[2],
            'Rule #2'
        );

        self::assertRuleReport(
            Status::Succeeded,
            [
                [Bar::class, State::Ignored, null],
                [Baz::class, State::Ignored, null],
                [Corge::class, State::Ignored, null],
                [Foo::class, State::Evaluated, Status::Succeeded],
                [Garply::class, State::Ignored, null],
                [Qux::class, State::Ignored, null],
                [Quux::class, State::Ignored, null],
                [Xyzzy::class, State::Ignored, null],
            ],
            $report[3],
            'Rule #3'
        );

        self::assertRuleReport(
            Status::Failed,
            [
                [Bar::class, State::Ignored, null],
                [Baz::class, State::Ignored, null],
                [Corge::class, State::Ignored, null],
                [Foo::class, State::Evaluated, Status::Failed],
                [Garply::class, State::Ignored, null],
                [Qux::class, State::Evaluated, Status::Succeeded],
                [Quux::class, State::Evaluated, Status::Succeeded],
                [Xyzzy::class, State::Ignored, null],
            ],
            $report[4],
            'Rule #4'
        );
    }

    /**
     * @param array{class-string, State, ?Status}[] $expectedValueReports
     */
    private static function assertRuleReport(
        Status $expectedStatus,
        array $expectedValueReports,
        mixed $report,
        string $message
    ): void {
        self::assertInstanceOf(RuleReport::class, $report, $message);
        self::assertEquals(8, $report->count(), $message);
        self::assertTrue($report->has($expectedStatus), $message);

        foreach ($expectedValueReports as $i => [$value, $state, $status]) {
            self::assertValueReport(
                $value,
                $state,
                $status,
                $report[$i],
                sprintf('%s - Class %s', $message, $value)
            );

        }
    }

    /**
     * @param class-string $expectedValue
     */
    private static function assertValueReport(
        string $expectedValue,
        State $expectedState,
        ?Status $expectedStatus,
        mixed $report,
        string $message
    ): void {
        self::assertInstanceOf(ValueReport::class, $report, $message);

        $value = $report->value();

        self::assertSame(
            $expectedValue,
            $value->getName(),
            sprintf(
                '%s : "%s" is not same as "%s"',
                $message,
                $value->getName(),
                $expectedValue
            )
        );

        self::assertTrue(
            $report->is($expectedState),
            sprintf(
                '%s : State is not equal to "%s"',
                $message,
                $expectedState->name
            )
        );

        if ($expectedStatus !== null) {
            self::assertTrue(
                $report->has($expectedStatus),
                sprintf(
                    '%s : Status is not equal to "%s"',
                    $message,
                    $expectedStatus->name
                )
            );
        }
    }
}
