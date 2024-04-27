<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures;

readonly class Quux
{
    public function foo(int $bar): int
    {
        return $bar;
    }
}
