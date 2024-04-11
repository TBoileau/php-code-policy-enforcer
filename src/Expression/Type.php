<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use ReflectionClass;

enum Type: string
{
    case Classes = ReflectionClass::class;
}
