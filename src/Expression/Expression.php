<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

use Closure;
use TBoileau\PhpCodePolicyEnforcer\Templating\Templating;

interface Expression
{
    /**
     * @param Closure(bool $result): void $onEvaluate
     */
    public function onEvaluate(Closure $onEvaluate): void;

    public function evaluate(mixed $value): bool;

    public function attachTo(LogicalExpression $parent): Expression;

    public function parent(): ?LogicalExpression;

    public function isRoot(): bool;

    public function level(): int;

    /**
     * @return string[]|string
     */
    public function message(Templating $templating): array | string;
}
