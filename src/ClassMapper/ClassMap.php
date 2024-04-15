<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\ClassMapper;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use LogicException;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use Traversable;

/**
 * @template-implements IteratorAggregate<ReflectionClass>
 * @template-implements ArrayAccess<class-string, ReflectionClass>
 */
final readonly class ClassMap implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @param array<class-string, ReflectionClass> $classes
     */
    public function __construct(private array $classes = [])
    {
    }

    public function getIterator(): Traversable
    {
        foreach ($this->classes as $class) {
            yield $class;
        }
    }

    public function count(): int
    {
        return count($this->classes);
    }public function offsetExists(mixed $offset): bool
    {
        return isset($this->classes[$offset]);
    }
    public function offsetGet(mixed $offset): ReflectionClass
    {
        return $this->classes[$offset];
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new LogicException('You cannot modify a class map.');
    }
    public function offsetUnset(mixed $offset): void
    {
        throw new LogicException('You cannot modify a class map.');
    }
}
