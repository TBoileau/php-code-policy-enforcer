<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report\Enum;

enum State: string
{
    case Ignored = '🗑️';
    case Evaluated = '⚙️';

    public function equals(State $state): bool
    {
        return $this === $state;
    }
}
