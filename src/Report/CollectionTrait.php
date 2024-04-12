<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Report;

use Traversable;

trait CollectionTrait
{
    public function getIterator(): Traversable
    {
        foreach ($this->children as $child) {
            yield $child;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->children[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->children[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->children[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->children[$offset]);
    }

    public function count(): int
    {
        return array_sum(array_map(count(...), $this->children));
    }
}
