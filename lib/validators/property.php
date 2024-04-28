<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property;

use ReflectionIntersectionType;
use ReflectionProperty;
use ReflectionUnionType;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;

function hasIntersectionType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasType',
        validator: static fn (ReflectionProperty $value): bool => $value->hasType() && $value->getType() instanceof ReflectionIntersectionType,
        message: 'has "type"',
        childExpression: $expression,
        childValues: static fn (ReflectionProperty $value): array => $value->hasType() && $value->getType() instanceof ReflectionIntersectionType
            ? $value->getType()->getTypes()
            : []
    );
}

function hasUnionType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasType',
        validator: static fn (ReflectionProperty $value): bool => $value->hasType() && $value->getType() instanceof ReflectionUnionType,
        message: 'has "type"',
        childExpression: $expression,
        childValues: static fn (ReflectionProperty $value): array => $value->hasType() && $value->getType() instanceof ReflectionUnionType
            ? $value->getType()->getTypes()
            : []
    );
}

function hasNamedType(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasType',
        validator: static fn (ReflectionProperty $value): bool => $value->hasType(),
        message: 'has "type"',
        childExpression: $expression,
        childValues: static fn (ReflectionProperty $value): array => $value->hasType()
            ? [$value->getType()]
            : []
    );
}

function isPromoted(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isPromoted',
        validator: static fn (ReflectionProperty $value): bool => $value->isPromoted(),
        message: 'is "promoted"',
    );
}

function isPrivate(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isPrivate',
        validator: static fn (ReflectionProperty $value): bool => $value->isPrivate(),
        message: 'is "private"',
    );
}

function isProtected(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isProtected',
        validator: static fn (ReflectionProperty $value): bool => $value->isProtected(),
        message: 'is "protected"',
    );
}

function isPublic(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isPublic',
        validator: static fn (ReflectionProperty $value): bool => $value->isPublic(),
        message: 'is "public"',
    );
}

function hasDefaultValue(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasDefaultValue',
        validator: static fn (ReflectionProperty $value): bool => $value->hasDefaultValue(),
        message: 'has "default value"',
    );
}

function isStatic(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isStatic',
        validator: static fn (ReflectionProperty $value): bool => $value->isStatic(),
        message: 'is "static"',
    );
}

function isReadOnly(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isReadOnly',
        validator: static fn (ReflectionProperty $value): bool => $value->isReadOnly(),
        message: 'is "read-only"',
    );
}

function matchWith(string $pattern): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'matchWith',
        validator: static fn (ReflectionProperty $value): bool => preg_match($pattern, $value->getName()) === 1,
        parameters: ['pattern' => $pattern],
        message: 'matches with pattern "{{ pattern }}"',
    );
}
