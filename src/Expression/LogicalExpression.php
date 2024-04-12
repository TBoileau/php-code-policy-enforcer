<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use ArrayAccess;
use Closure;
use IteratorAggregate;
use LogicException;
use TBoileau\PhpCodePolicyEnforcer\Evaluator\Evaluator;
use TBoileau\PhpCodePolicyEnforcer\Templating\Templating;
use Traversable;

use function Symfony\Component\String\u;

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

    private ?Closure $onEvaluate = null;

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

    public function isRoot(): bool
    {
        return $this->level() === 0;
    }

    public function level(): int
    {
        return null === $this->parent ? 0 : $this->parent->level() + 1;
    }

    public function add(Expression $expression): void
    {
        $this->children[] = $expression->attachTo($this);
    }

    public function onEvaluate(Closure $onEvaluate): void
    {
        $this->onEvaluate = $onEvaluate;

        foreach ($this->children as $child) {
            $child->onEvaluate($onEvaluate);
        }
    }

    public function evaluate(mixed $value): bool
    {
        $result = Evaluator::evaluate($this, $value);

        if (null === $this->onEvaluate) {
            throw new LogicException('onEvaluate must be set');
        }

        $this->onEvaluate->call($this, $result);

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

    public function message(Templating $templating): array | string
    {
        if (count($this->children) === 1) {
            return $this->children[0]->message($templating);
        }

        if ($this->operator === Operator::Not) {
            $messages = $this->children[0]->message($templating);

            if (is_string($messages)) {
                return $templating->render('not {{ message }}', ['message' => $messages]);
            }

            $messages[0] = $templating->render('not {{ message }}', ['message' => $messages[0]]);

            return $messages;
        }

        $messages = [];

        foreach ($this->children as $i => $child) {
            $childMessages = $child->message($templating);
            $indentation = $i === 0 ? '' : u('')->padStart($child->level() * 2, ' ');
            $operator = $i === 0 ? '' : sprintf('%s ', $this->operator->value);

            if (is_string($childMessages)) {
                $messages[] = $templating->render(
                    '{{ indentation }}{{ operator }}{{ message }}',
                    [
                        'indentation' => $indentation,
                        'message' => $childMessages,
                        'operator' => $operator
                    ]
                );
                continue;
            }

            foreach ($childMessages as $k => $childMessage) {
                $messages[] = $templating->render(
                    '{{ indentation }} {{ operator }} {{ message }}',
                    [
                        'indentation' => $indentation,
                        'message' => $childMessage,
                        'operator' => $this->operator->value
                    ]
                );
            }
        }

        return $messages;
    }
}
