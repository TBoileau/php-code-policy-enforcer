<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<RuleSet>
 */
final class CodePolicy implements Countable, IteratorAggregate
{
    /**
     * @var RuleSet[]
     */
    private array $ruleSets = [];

    public function add(RuleSet $ruleSet): self
    {
        $this->ruleSets[] = $ruleSet;

        return $this;
    }

    /**
     * @return Traversable<RuleSet>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->ruleSets as $ruleSet) {
            yield $ruleSet;
        }
    }

    public function count(): int
    {
        return count($this->ruleSets);
    }
}
