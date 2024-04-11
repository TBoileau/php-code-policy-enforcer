<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\ClassMapper;

use Countable;
use IteratorAggregate;
use ReflectionClass;
use ReflectionException;
use Traversable;

/**
 * @template-implements IteratorAggregate<ReflectionClass>
 */
final readonly class ClassMap implements Countable, IteratorAggregate
{
    /**
     * @param ReflectionClass[] $classes
     */
    private function __construct(private array $classes = [])
    {
    }

    /**
     * @param class-string[] $fqcn
     * @return self
     * @throws ReflectionException
     */
    public static function fromArrayOfFqcn(array $fqcn): self
    {
        return new self(
            array_map(
                static fn (string $fqcn) => new ReflectionClass($fqcn),
                $fqcn
            )
        );
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
