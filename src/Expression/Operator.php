<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

enum Operator: string
{
    case And = 'and';
    case Or = 'or';
    case Not = 'not';
    case Xor = 'xor';

    public function equals(Operator $operator): bool
    {
        return $this === $operator;
    }
}
