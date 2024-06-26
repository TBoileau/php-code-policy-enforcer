<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method;

use LogicException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Comparison\equalTo;

function containsParameters(int|array $comparison): ConditionalExpression
{
    if (is_int($comparison)) {
        $comparison = equalTo($comparison);
    }

    ['expectedValue' => $expectedValue, 'callback' => $callback] = $comparison;

    return new ConditionalExpression(
        name: 'containsParameters',
        validator: static fn (ReflectionMethod $value): bool => $callback(count($value->getParameters())),
        parameters: ['numberOfParameters' => $expectedValue],
        message: 'contains "{{ numberOfParameters }}" {{ "parameter"|inflect(numberOfParameters) }}',
    );
}

function hasParameter(string $parameter, ?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasParameter',
        validator: static fn (ReflectionMethod $value): bool => count(
            array_filter(
                $value->getParameters(),
                static fn (ReflectionParameter $param): bool => $param->getName() === $parameter
            )
        ) === 1,
        parameters: ['parameter' => $parameter],
        message: 'has a parameter named "{{ parameter }}"',
        childExpression: $expression,
        childValues: static fn (ReflectionMethod $value): array => array_filter(
            $value->getParameters(),
            static fn (ReflectionParameter $param): bool => $param->getName() === $parameter
        )
    );
}

function hasReturnType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasReturnType',
        validator: static fn (ReflectionMethod $value): bool => $value->hasReturnType(),
        message: 'has a "return type"',
        childExpression: $expression,
        childValues: static fn (ReflectionMethod $value): array => $value->hasReturnType()
            ? (
                $value->getReturnType() instanceof ReflectionNamedType
                    ? [$value->getReturnType()]
                    : $value->getReturnType()->getTypes()
            )
            : []
    );
}

function isAbstract(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isAbstract',
        validator: static fn (ReflectionMethod $value): bool => $value->isAbstract(),
        message: 'is "abstract"',
    );
}

function isFinal(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isFinal',
        validator: static fn (ReflectionMethod $value): bool => $value->isFinal(),
        message: 'is "final"',
    );
}

function isPrivate(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isPrivate',
        validator: static fn (ReflectionMethod $value): bool => $value->isPrivate(),
        message: 'is "private"',
    );
}

function isProtected(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isProtected',
        validator: static fn (ReflectionMethod $value): bool => $value->isProtected(),
        message: 'is "protected"',
    );
}

function isPublic(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isPublic',
        validator: static fn (ReflectionMethod $value): bool => $value->isPublic(),
        message: 'is "public"',
    );
}

function isStatic(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isStatic',
        validator: static fn (ReflectionMethod $value): bool => $value->isStatic(),
        message: 'is "static"',
    );
}

function matchWith(string $pattern): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'matchWith',
        validator: static fn (ReflectionMethod $value): bool => preg_match($pattern, $value->getName()) === 1,
        parameters: ['pattern' => $pattern],
        message: 'matches with pattern "{{ pattern }}"',
    );
}
