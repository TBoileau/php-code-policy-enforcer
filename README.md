# PHP Code Policy Enforcer

## Usage

```php
<?php 

declare(strict_types=1);

use TBoileau\PhpCodePolicyEnforcer\Rule;

Rule::create()
    ->that()->classes()
        ->andX()
            ->residesIn('TBoileau\PhpCodePolicyEnforcer\Tests')
            ->isInstantiable()
        ->end()
    ->should()
        ->orX()
            ->not()->isFinal()
            ->isEnum()
        ->end()
    ->because('Classes must be instantiable and not final or enum');
Rule::create()
    ->that()->
        ->andX()
            ->residesIn('TBoileau\PhpCodePolicyEnforcer\Tests')
            ->isInstantiable()
        ->end()
    ->should()
        ->orX()
            ->not()->isFinal()
            ->isEnum()
        ->end()
    ->because('Classes must be instantiable and not final or enum');
```