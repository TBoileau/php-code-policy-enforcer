<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures;

use Attribute;
use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault\{Garply, Qux as Q};
use Traversable;

/**
 * @implements IteratorAggregate<int>
 */
#[Attribute]
final class Foo extends Q implements Bar, Garply, Countable, IteratorAggregate
{
    use Corge;

    public const WALDO = 'waldo';

    public string $plugh = 'plugh';

    public function fred(Quux $quux): void
    {
    }

    public function count(): int
    {
        return 1;
    }

    public function __invoke(int $bar): void
    {
    }

    public function getIterator(): Traversable
    {
        yield from [];
    }
}
