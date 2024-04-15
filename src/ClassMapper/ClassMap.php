<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\ClassMapper;

use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use Traversable;

/**
 * @template-implements IteratorAggregate<ReflectionClass>
 */
final readonly class ClassMap implements Countable, IteratorAggregate
{
    /**
     * @param ReflectionClass[] $classes
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
    }
}
