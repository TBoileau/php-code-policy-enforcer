<?php

declare(strict_types=1);

use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Rule;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\implementsInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInstantiable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isReadOnly;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\not;

return CodePolicy::analyze(__DIR__ . '/../src')
    ->add(
        Rule::classes()
            ->that(
                residesIn('TBoileau\PhpCodePolicyEnforcer\Report'),
                isInstantiable()
            )
            ->should(
                isFinal(),
                matchWith('/.+Report$/')
            )
            ->because('All output classes must be immutable')
    )
    ->add(
        Rule::classes()
            ->that(
                residesIn('TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy'),
                not(isInterface())
            )
            ->should(
                implementsInterface(Strategy::class),
                isFinal(),
                isReadOnly(),
                matchWith('/.+Evaluator/')
            )
            ->because('Use strategy pattern to evaluate logical expressions')
    );
