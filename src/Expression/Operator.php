<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

enum Operator
{
    case And;
    case Or;
    case Not;
    case Xor;
}
