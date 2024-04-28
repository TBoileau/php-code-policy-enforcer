<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;

use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Bar;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Xyzzy;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Helpers\that;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\orX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\xorX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\containsMethods;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\implementsInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isSubclassOf;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\uses;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\containsParameters;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasParameter;

return CodePolicy::in(__DIR__ . '/../../src')
    ->add(
        that(residesIn('TBoileau\PhpCodePolicyEnforcer'))
            ->should(not(isSubclassOf(TestCase::class)))
            ->because('rule 2')
    )
    ->add(
        that(
            residesIn('TBoileau\PhpCodePolicyEnforcer'),
            orX(
                containsMethods(5),
                not(isFinal()),
                andX(
                    isEnum(),
                    uses(Xyzzy::class),
                    not(implementsInterface(Bar::class))
                )
            )
        )
            ->should(
                hasMethod('test'),
                orX(
                    not(isAbstract()),
                    xorX(
                        not(isInterface()),
                        hasMethod(
                            'test',
                            andX(
                                containsParameters(2),
                                hasParameter('foo'),
                                orX(
                                    hasParameter('bar'),
                                    hasParameter('baz')
                                )
                            )
                        )
                    )
                )
            )
            ->because('this is a test')
    );
