<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer;

use Cake\Chronos\Chronos;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Operator;

final readonly class Compiler
{
    public static function compile(Rule $rule): array
    {
        return [
            'date' => Chronos::now()->toDateTimeString(),
            'type' => $rule->type()->name,
            'that' => self::compileExpression($rule->getThat()),
            'should' => self::compileExpression($rule->getShould()),
            'reason' => $rule->reason()
        ];
    }

    private static function compileExpression(Expression $expression): array
    {
        if ($expression instanceof ConditionalExpression) {
            return [
                'not' => $expression->parent()->operator() === Operator::Not,
                'validator' => $expression->name(),
                'parameters' => $expression->parameters(),
            ];
        }

        if ($expression->operator() === Operator::Not) {
            return self::compileExpression($expression[0]);
        }

        if ($expression->parent() === null) {
            return $expression->map(static fn (Expression $expression): array => self::compileExpression($expression));
        }

        return [
            'not' => $expression->parent()->operator() === Operator::Not,
            'operator' => $expression->operator()->name,
            'children' => $expression->map(static fn (Expression $expression): array => self::compileExpression($expression))
        ];
    }
}
