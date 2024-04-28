<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter;

use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionUnionType;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;

function hasIntersectionType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasType',
        validator: static fn (ReflectionParameter $value): bool => $value->hasType() && $value->getType() instanceof ReflectionIntersectionType,
        message: 'has "type"',
        childExpression: $expression,
        childValues: static fn (ReflectionParameter $value): array => $value->hasType() && $value->getType() instanceof ReflectionIntersectionType
            ? $value->getType()->getTypes()
            : []
    );
}
function hasUnionType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasType',
        validator: static fn (ReflectionParameter $value): bool => $value->hasType() && $value->getType() instanceof ReflectionUnionType,
        message: 'has "type"',
        childExpression: $expression,
        childValues: static fn (ReflectionParameter $value): array => $value->hasType() && $value->getType() instanceof ReflectionUnionType
            ? $value->getType()->getTypes()
            : []
    );
}

function hasNamedType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasType',
        validator: static fn (ReflectionParameter $value): bool => $value->hasType(),
        message: 'has "type"',
        childExpression: $expression,
        childValues: static fn (ReflectionParameter $value): array => $value->hasType()
            ? [$value->getType()]
            : []
    );
}

function hasDefaultValue(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasDefaultValue',
        validator: static fn (ReflectionParameter $value): bool => $value->isDefaultValueAvailable(),
        message: 'has "default value"',
    );
}

function isPassedByReference(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isPassedByReference',
        validator: static fn (ReflectionParameter $value): bool => $value->isPassedByReference(),
        message: 'is passed by "reference"',
    );
}

function isVariadic(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isVariadic',
        validator: static fn (ReflectionParameter $value): bool => $value->isVariadic(),
        message: 'is "variadic"',
    );
}

function isOptional(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isOptional',
        validator: static fn (ReflectionParameter $value): bool => $value->isOptional(),
        message: 'is "optional"',
    );
}

function matchWith(string $pattern): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'matchWith',
        validator: static fn (ReflectionParameter $value): bool => preg_match($pattern, $value->getName()) === 1,
        parameters: ['pattern' => $pattern],
        message: 'matches with pattern "{{ pattern }}"',
    );
}
