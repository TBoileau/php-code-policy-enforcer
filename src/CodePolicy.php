<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Countable;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMap;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMapper;
use Traversable;

/**
 * @template-implements IteratorAggregate<Rule>
 */
final class CodePolicy implements Countable, IteratorAggregate
{
    /**
     * @var Rule[]
     */
    private array $rules = [];

    private function __construct(private readonly ClassMap $classMap)
    {
    }

    public static function in(string ...$directories): self
    {
        $classMapper = new ClassMapper();

        foreach ($directories as $directory) {
            $classMapper->add($directory);
        }

        return new self($classMapper->generate());
    }

    public function classMap(): ClassMap
    {
        return $this->classMap;
    }

    public function add(Rule $rule): self
    {
        $this->rules[] = $rule->setCodePolicy($this);

        return $this;
    }

    /**
     * @return Traversable<Rule>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->rules as $rule) {
            yield $rule;
        }
    }

    public function count(): int
    {
        return count($this->rules) * count($this->classMap);
    }
}
