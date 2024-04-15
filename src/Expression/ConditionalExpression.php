<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use Closure;
use LogicException;
use TBoileau\PhpCodePolicyEnforcer\Templating\Templating;

final class ConditionalExpression implements Expression
{
    private LogicalExpression $parent;

    private ?Closure $onEvaluate = null;

    /**
     * @param Closure(mixed): bool $validator
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly string  $name,
        private readonly Closure $validator,
        private readonly array   $parameters = [],
        private readonly string  $message = ''
    ) {
    }

    public function onEvaluate(Closure $onEvaluate): void
    {
        $this->onEvaluate = $onEvaluate;
    }

    public function attachTo(LogicalExpression $parent): Expression
    {
        $this->parent = $parent;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function evaluate(mixed $value): bool
    {
        $result = ($this->validator)($value);

        if (null !== $this->onEvaluate) {
            $this->onEvaluate->call($this, $result);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function parent(): LogicalExpression
    {
        return $this->parent;
    }

    public function isRoot(): bool
    {
        return false;
    }

    public function level(): int
    {
        return $this->parent->level() + 1;
    }

    public function message(Templating $templating): string
    {
        return $templating->render($this->message, $this->parameters);
    }
}
