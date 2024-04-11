<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\Compiler;
use TBoileau\PhpCodePolicyEnforcer\Rule;

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

final class CompilerTest extends TestCase
{
    #[Test]
    public function shouldCompileSuccessfully(): void
    {
        Chronos::setTestNow('2024-01-01 00:00:00');

        $compiledRule = Compiler::compile(
            Rule::classes()
                    ->that(residesIn('App/Foo'))
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
        );

        self::assertEquals([
            'date' => '2024-01-01 00:00:00',
            'type' => 'Classes',
            'that' => [
                [
                    'not' => false,
                    'validator' => 'residesIn',
                    'parameters' => ['namespace' => 'App/Foo'],
                ]
            ],
            'should' => [
                [
                    'not' => false,
                    'operator' => 'Or',
                    'children' => [
                        [
                            'not' => false,
                            'validator' => 'isEnum',
                            'parameters' => [],
                        ],
                        [
                            'not' => true,
                            'validator' => 'isInterface',
                            'parameters' => [],
                        ],
                        [
                            'not' => false,
                            'operator' => 'Xor',
                            'children' => [
                                [
                                    'not' => false,
                                    'validator' => 'isFinal',
                                    'parameters' => [],
                                ],
                                [
                                    'not' => true,
                                    'validator' => 'isAbstract',
                                    'parameters' => [],
                                ]
                            ]
                        ],
                        [
                            'not' => true,
                            'operator' => 'And',
                            'children' => [
                                [
                                    'not' => false,
                                    'validator' => 'hasMethod',
                                    'parameters' => ['method' => '__invoke']
                                ],
                                [
                                    'not' => false,
                                    'validator' => 'hasProperty',
                                    'parameters' => ['property' => 'bar']
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'not' => false,
                    'validator' => 'matchWith',
                    'parameters' => ['pattern' => '/.+Controller$/']
                ],
            ],
            'reason' => 'reason',
        ], $compiledRule);
    }
}
