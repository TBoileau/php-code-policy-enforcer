<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Expression;

trait NestedExpressionTrait
{
    private ?Expression $parent = null;

    public function getParent(): ?Expression
    {
        return $this->parent;
    }

    public function setParent(Expression $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function isChildExpression(): bool
    {
        if ($this->parent === null) {
            return false;
        }

        if ($this->parent instanceof ConditionalExpression) {
            return true;
        }

        return $this->parent->isChildExpression();
    }

    public function getTrace(): array
    {
        $trace = [];

        $current = $this;

        while (null !== $current) {
            if ($current instanceof ConditionalExpression) {
                $trace[] = $current;
            }
            $current = $current->getParent();
        }

        return array_reverse($trace);
    }

    public function getLevel(): int
    {
        return null === $this->parent ? 0 : $this->parent->getLevel() + 1;
    }
}
