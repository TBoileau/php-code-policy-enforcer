<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical;

use LogicException;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Expression\LogicalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Operator;

function addLogicalExpression(Operator $operator, Expression ...$expressions): Expression
{
    $expression = new LogicalExpression($operator);

    foreach ($expressions as $childExpression) {
        $expression->add($childExpression);
    }

    return $expression;
}

function andX(Expression ...$expressions): Expression
{
    if (count($expressions) < 2) {
        throw new LogicException('You must provide at least two expressions');
    }

    return addLogicalExpression(Operator::And, ...$expressions);
}

function orX(Expression ...$expressions): Expression
{
    if (count($expressions) < 2) {
        throw new LogicException('You must provide at least two expressions');
    }

    return addLogicalExpression(Operator::Or, ...$expressions);
}

function xorX(Expression ...$expressions): Expression
{
    if (count($expressions) < 2) {
        throw new LogicException('You must provide at least two expressions');
    }

    return addLogicalExpression(Operator::Xor, ...$expressions);
}

function not(ConditionalExpression $expression): Expression
{
    if (null !== $expression->getChildExpression()) {
        throw new LogicException('You cannot negate an expression with a child expression');
    }

    $expression->setNot(true);

    return $expression;
}
