<?php

declare(strict_types=1);

use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Bar;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Helpers\that;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Comparison\equalTo;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\orX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\implementsInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInstantiable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isReadOnly;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\containsParameters;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasParameter;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasReturnType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isPublic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\hasNamedType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\is;

return CodePolicy::in(__DIR__ . '/../../src')
    ->add(
        that(
            residesIn('TBoileau\PhpCodePolicyEnforcer'),
        )
            ->should(
                isFinal(),
                isReadOnly(),
                hasMethod(
                    '__invoke',
                    andX(
                        containsParameters(equalTo(1)),
                        isPublic(),
                        orX(
                            andX(
                                hasParameter(
                                    'input',
                                    hasNamedType(
                                        is(
                                            andX(
                                                isInstantiable(),
                                                isFinal(),
                                                isReadOnly(),
                                                matchWith('/Input$/'),
                                                implementsInterface(Bar::class),
                                            ),
                                        ),
                                    ),
                                ),
                                hasReturnType(
                                    hasNamedType(
                                        is(
                                            andX(
                                                isInstantiable(),
                                                isFinal(),
                                                isReadOnly(),
                                                matchWith('/Output$/'),
                                            ),
                                        ),
                                    ),
                                ),
                                hasParameter('input'),
                            ),
                            andX(
                                hasParameter(
                                    'input',
                                    hasNamedType(
                                        is(
                                            andX(
                                                isInstantiable(),
                                                isFinal(),
                                                isReadOnly(),
                                                matchWith('/Input$/'),
                                                implementsInterface(Bar::class),
                                            ),
                                        ),
                                    ),
                                ),
                                orX(
                                    not(hasReturnType()),
                                    hasReturnType(
                                        hasNamedType(
                                            is(
                                                andX(
                                                    isInstantiable(),
                                                    isFinal(),
                                                    isReadOnly(),
                                                    matchWith('/Output$/'),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
            ->because('rule 2')
    );
