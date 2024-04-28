<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type;

use ReflectionNamedType;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;

function is(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isClass',
        validator: static fn (ReflectionNamedType $value): bool => !$value->isBuiltin() && !in_array($value->getName(), ['static', 'self', 'parent']),
        message: 'is a "class"',
        childExpression: $expression,
        childValues: static fn (ReflectionNamedType $value): array => !$value->isBuiltin() && !in_array($value->getName(), ['static', 'self', 'parent'])
            ? [new ReflectionClass($value->getName(), [])]
            : []
    );
}

function isClass(string $class, ?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isClass',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === $class,
        message: 'is a "class"',
        childExpression: $expression,
        childValues: static fn (ReflectionNamedType $value): array => $value->getName() === $class
            ? [new ReflectionClass($value->getName(), [])]
            : []
    );
}

function isStatic(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isStatic',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'static',
        message: 'is relative to "static"',
    );
}

function isSelf(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isSelf',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'self',
        message: 'is relative to "self"',
    );
}

function isParent(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isParent',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'parent',
        message: 'is relative to "parent"',
    );
}

function isVoid(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isVoid',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'void',
        message: 'is "void"',
    );
}

function isTrue(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isTrue',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'true',
        message: 'is a "true"',
    );
}

function isNever(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isNever',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'never',
        message: 'is a "never"',
    );
}

function isMixed(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isMixed',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'mixed',
        message: 'is a "mixed"',
    );
}

function isIterable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isIterable',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'iterable',
        message: 'is an "iterable"',
    );
}

function isFalse(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isFalse',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'false',
        message: 'is "false"',
    );
}

function isString(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isString',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'string',
        message: 'is a "string"',
    );
}

function isObject(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isObject',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'object',
        message: 'is an "object"',
    );
}

function isNull(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isNull',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'null',
        message: 'is "null"',
    );
}

function isInteger(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInteger',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'int',
        message: 'is an "integer"',
    );
}

function isFloat(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isFloat',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'float',
        message: 'is a "float"',
    );
}

function isCallable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isCallable',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'callable',
        message: 'is a "callable"',
    );
}

function isBoolean(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isBoolean',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'bool',
        message: 'is a "boolean"',
    );
}

function isArray(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isArray',
        validator: static fn (ReflectionNamedType $value): bool => $value->getName() === 'array',
        message: 'is an "array"',
    );
}

function isBuiltIn(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isBuiltIn',
        validator: static fn (ReflectionNamedType $value): bool => $value->isBuiltIn(),
        message: 'is "built-in"',
    );
}
