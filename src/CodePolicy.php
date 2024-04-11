<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<ClassSet>
 */
final class CodePolicy implements Countable, IteratorAggregate
{
    /**
     * @var ClassSet[]
     */
    private array $classSets = [];

    public function add(ClassSet $classSet): self
    {
        $this->classSets[] = $classSet;

        return $this;
    }

    /**
     * @return Traversable<ClassSet>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->classSets as $classSet) {
            yield $classSet;
        }
    }

    public function count(): int
    {
        return array_sum(array_map('count', $this->classSets));
    }
}
