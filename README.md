# PHP Code Policy Enforcer

## Configuration
Example of configuration file `php-code-policy-enforcer.php`:

```php
<?php

declare(strict_types=1);

use TBoileau\PhpCodePolicyEnforcer\RuleSet;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Rule;

use function TBoileau\PhpCodePolicyEnforcer\Lib\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasProperty;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\orX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\xorX;

return (new CodePolicy())->add(
    RuleSet::scan(__DIR__ . '/../src')
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

![](https://github.com/Your_Repository_Name/Your_GIF_Name.gif)