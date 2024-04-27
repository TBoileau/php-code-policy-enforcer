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
 * @implements IteratorAggregate<ReflectionClass>
 * @implements ArrayAccess<class-string, ReflectionClass>
 */
final class ClassMap implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var array<class-string, ReflectionClass>
     */
    private array $classes = [];

    public function __construct(private readonly ClassMapper $classMapper)
    {
    }

    public function classMapper(): ClassMapper
    {
        return $this->classMapper;
    }

    public function add(ReflectionClass $class): void
    {
        $this->classes[$class->getName()] = $class;
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
    }

    public function offsetExists(mixed $offset): bool
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
