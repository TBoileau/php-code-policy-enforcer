<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures;

use Attribute;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault\Garply;
use Traversable;

/**
 * @implements IteratorAggregate<int>
 */
#[Attribute]
final class Foo extends Qux implements Bar, Garply, Countable, IteratorAggregate
{
    use Corge;

    public const WALDO = 'waldo';

    public string $plugh = 'plugh';

    public function fred(): void
    {
    }

    public function count(): int
    {
        return 1;
    }

    public function __invoke(): void
    {
    }

    public function getIterator(): Traversable
    {
        yield from [];
    }
}
