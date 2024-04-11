<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Closure;

final readonly class Runner
{
    public function __construct(private CodePolicy $codePolicy)
    {
    }

    /**
     * @return iterable<Result|null>
     */
    public function run(): iterable
    {
        foreach ($this->codePolicy as $classSet) {
            foreach ($classSet as $rule) {
                yield from $rule->check();
            }
        }
    }
}
