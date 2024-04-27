<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use ArrayAccess;
use Closure;
use IteratorAggregate;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Evaluator;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;
use Traversable;

use function Symfony\Component\String\u;

/**
 * @implements IteratorAggregate<Expression>
 * @implements ArrayAccess<int, Expression>
 */
final class LogicalExpression implements Expression, IteratorAggregate, ArrayAccess
{
    use NestedExpressionTrait;

    /**
     * @var Expression[]
     */
    private array $children = [];

    public function __construct(private readonly Operator $operator = Operator::And)
    {
    }

    public function is(Operator $operator): bool
    {
        return $operator->equals($this->operator);
    }

    public function evaluate(Report $report): bool
    {
        return Evaluator::evaluate($this, $report);
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function add(Expression $expression): void
    {
        $this->children[] = $expression->setParent($this);
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
