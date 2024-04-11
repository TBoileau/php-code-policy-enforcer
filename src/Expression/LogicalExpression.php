<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use ArrayAccess;
use Closure;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<Expression>
 * @implements ArrayAccess<int, Expression>
 */
final class LogicalExpression implements Expression, IteratorAggregate, ArrayAccess
{
    /**
     * @var Expression[]
     */
    private array $children = [];

    private ?LogicalExpression $parent = null;

    public function __construct(private readonly Operator $operator = Operator::And)
    {
    }

    public function attachTo(LogicalExpression $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function operator(): Operator
    {
        return $this->operator;
    }

    public function parent(): ?LogicalExpression
    {
        return $this->parent;
    }

    public function add(Expression $expression): void
    {
        $this->children[] = $expression->attachTo($this);
    }

    public function evaluate(mixed $value, ?Closure $on = null): bool
    {
        $result = match ($this->operator) {
            Operator::And => (function (mixed $value, ?Closure $on): bool {
                foreach ($this as $child) {
                    if (!$child->evaluate($value, $on)) {
                        return false;
                    }
                }

                return true;
            })($value, $on),
            Operator::Or => (function (mixed $value, ?Closure $on): bool {
                foreach ($this as $child) {
                    if (!$child->evaluate($value, $on)) {
                        return true;
                    }
                }

                return false;
            })($value, $on),
            Operator::Xor => (function (mixed $value, ?Closure $on): bool {
                $count = 0;
                foreach ($this as $child) {
                    if ($child->evaluate($value, $on)) {
                        $count++;
                    }
                }

                return 1 === $count;
            })($value, $on),
            Operator::Not => (function (mixed $value, ?Closure $on): bool {
                return !$this->children[0]->evaluate($value, $on);
            })($value, $on),
        };

        if (null !== $on) {
            $on($this, $result, $value);
        }

        return $result;
    }

    /**
     * @return Traversable<Expression>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->children as $expression) {
            yield $expression;
        }
    }

    /**
     * @template TMap
     * @param Closure(Expression $expression): TMap $callback
     * @return TMap[]
     */
    public function map(Closure $callback): array
    {
        return array_map($callback, $this->children);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->children[$offset]);
    }

    public function offsetGet(mixed $offset): Expression
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
}
