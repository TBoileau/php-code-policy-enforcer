<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use LogicException;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMap;
use TBoileau\PhpCodePolicyEnforcer\Expression\Condition;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Type;

final class Rule
{
    private ?LogicalExpression $that = null;

    private ?LogicalExpression $should = null;

    private ?string $reason = null;

    private ?ClassMap $classMap = null;

    /**
     * @param Type $type
     */
    private function __construct(private readonly Type $type)
    {
    }

    public function init(ClassMap $classMap): void
    {
        $this->classMap = $classMap;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public static function classes(): Rule
    {
        return new self(Type::Classes);
    }

    public function that(Expression ...$expressions): Rule
    {
        $this->that = new LogicalExpression();

        foreach ($expressions as $expression) {
            $this->that->add($expression);
        }

        return $this;
    }

    public function should(Expression ...$expressions): Rule
    {
        $this->should = new LogicalExpression();

        foreach ($expressions as $expression) {
            $this->should->add($expression);
        }

        return $this;
    }

    public function because(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function reason(): string
    {
        if (null === $this->reason) {
            throw new LogicException('You must provide a reason');
        }

        return $this->reason;
    }

    /**
     * @return iterable<Result|null>
     */
    public function check(): iterable
    {
        if (null === $this->classMap) {
            throw new LogicException('You must provide a class map');
        }

        /** @var Result[] $results */
        $results = [];

        $on = static function (Expression $expression, bool $result, mixed $value) use (&$results): void {
            if ($expression instanceof Condition) {
                $results[] = new Result($expression, $result, $value);

                return;
            }

            $parent = new Result($expression, $result, $value);

            foreach ($results as $tempResult) {
                $parent->add($tempResult);
            }

            $results = [$parent];
        };

        foreach ($this->classMap as $class) {
            if (!$this->getThat()->evaluate($class, null)) {
                yield null;
                continue;
            }

            $on($this->getShould(), $this->getShould()->evaluate($class, $on), $class);

            yield $results[0];

            $results = [];
        }

        return $results;
    }

    public function getThat(): LogicalExpression
    {
        if (null === $this->that) {
            throw new LogicException('You must provide a filter');
        }

        return $this->that;
    }

    public function getShould(): LogicalExpression
    {
        if (null === $this->should) {
            throw new LogicException('You must provide an evaluator');
        }

        return $this->should;
    }
}
