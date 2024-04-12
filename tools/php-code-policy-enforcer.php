<?php

declare(strict_types=1);

use TBoileau\PhpCodePolicyEnforcer\Evaluator\Strategy;
use TBoileau\PhpCodePolicyEnforcer\RuleSet;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Rule;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\implementsInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isInstantiable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\isReadOnly;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\not;

return (new CodePolicy())->add(
    RuleSet::scan(__DIR__ . '/../src')
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
        )
);
