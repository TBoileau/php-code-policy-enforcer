# PHP Code Policy Enforcer

## Configuration
Example of configuration file `php-code-policy-enforcer.php`:
```php
<?php

declare(strict_types=1);

use TBoileau\PhpCodePolicyEnforcer\ClassSet;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
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

return (new CodePolicy())->add(
    ClassSet::scan(__DIR__ . '/../src')
        ->add(
            Rule::classes()
                ->that(residesIn('TBoileau\PhpCodePolicyEnforcer'))
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

```

## Usage 
```bash
php bin-stub/php-code-policy-enforcer check --config=php-code-policy-enforcer.php
```
