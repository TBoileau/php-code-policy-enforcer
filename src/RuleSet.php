<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use ReflectionException;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMap;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMapper;
use Traversable;

/**
 * @template-implements IteratorAggregate<Rule>
 */
final class RuleSet implements Countable, IteratorAggregate
{
    /**
     * @var Rule[]
     */
    private array $rules = [];

    private readonly ClassMap $classMap;

    /**
     * @throws ReflectionException
     */
    private function __construct(private readonly string $directory)
    {
        $this->classMap = ClassMapper::generateClassMap($this->directory);
    }

    public function classMap(): ClassMap
    {
        return $this->classMap;
    }

    public function add(Rule $rule): self
    {
        $rule->init($this->classMap);

        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public static function scan(string $directory): self
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
        }

        return new self($directory);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->rules as $rule) {
            yield $rule;
        }
    }

    public function count(): int
    {
        return count($this->rules);
    }
}
