<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\ClassSet;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Result;
use TBoileau\PhpCodePolicyEnforcer\Rule;
use TBoileau\PhpCodePolicyEnforcer\Runner;

use function TBoileau\PhpCodePolicyEnforcer\Lib\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\hasProperty;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isInterface;
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
        $codePolicy = (new CodePolicy())->add(
            ClassSet::scan(__DIR__ . '/Fixtures')
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
                    ->because('reason')
                )
        );

        $runner = new Runner($codePolicy);

        self::assertCount(6, $codePolicy);

        $results = iterator_to_array($runner->run());

        self::assertCount(6, $results);
        self::assertCount(0, array_filter($results, static fn (?Result $result): bool => $result !== null && $result->result()));
    }
}
