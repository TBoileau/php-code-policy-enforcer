<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Helpers\that;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isSubclassOf;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;

return CodePolicy::in(__DIR__ . '/../../src')
    ->add(
        that(residesIn('TBoileau\PhpCodePolicyEnforcer'))
            ->should(not(isSubclassOf(TestCase::class)))
            ->because('rule 2')
    );
