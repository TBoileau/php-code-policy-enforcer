<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Class;

use ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Expression\Condition;

function hasAttribute(string $attribute): Condition
{
    return new Condition(
        name: 'hasAttribute',
        validator: static fn (ReflectionClass $value): bool => count($value->getAttributes($attribute)) > 0,
        parameters: ['attribute' => $attribute],
        message: 'Class "{{ value.name }}" does not have attribute {{ attribute }}',
    );
}

function hasConstant(string $constant): Condition
{
    return new Condition(
        name: 'hasConstant',
        validator: static fn (ReflectionClass $value): bool => $value->hasConstant($constant),
        parameters: ['constant' => $constant],
        message: 'Class "{{ value.name }}" does not have constant "{{ constant }}"',
    );
}

function hasMethod(string $method): Condition
{
    return new Condition(
        name: 'hasMethod',
        validator: static fn (ReflectionClass $value): bool => $value->hasMethod($method),
        parameters: ['method' => $method],
        message: 'Class "{{ value.name }}" does not have method "{{ method }}"',
    );
}

function hasProperty(string $property): Condition
{
    return new Condition(
        name: 'hasProperty',
        validator: static fn (ReflectionClass $value): bool => $value->hasProperty($property),
        parameters: ['property' => $property],
        message: 'Class "{{ value.name }}" does not have property "{{ property }}"',
    );
}

function implementsInterface(string $interface): Condition
{
    return new Condition(
        name: 'implementsInterface',
        validator: static fn (ReflectionClass $value): bool => $value->implementsInterface($interface),
        parameters: ['interface' => $interface],
        message: 'Class "{{ value.name }}" does not implement interface "{{ interface }}"',
    );
}

function inNamespace(): Condition
{
    return new Condition(
        name: 'inNamespace',
        validator: static fn (ReflectionClass $value): bool => $value->inNamespace(),
        message: 'Class "{{ value.name }}" is not in a namespace',
    );
}

function isAbstract(): Condition
{
    return new Condition(
        name: 'isAbstract',
        validator: static fn (ReflectionClass $value): bool => $value->isAbstract(),
        message: 'Class "{{ value.name }}" is not abstract',
    );
}

function isAnonymous(): Condition
{
    return new Condition(
        name: 'isAnonymous',
        validator: static fn (ReflectionClass $value): bool => $value->isAnonymous(),
        message: 'Class "{{ value.name }}" is not anonymous',
    );
}

function isCloneable(): Condition
{
    return new Condition(
        name: 'isCloneable',
        validator: static fn (ReflectionClass $value): bool => $value->isCloneable(),
        message: 'Class "{{ value.name }}" is not cloneable',
    );
}

function isEnum(): Condition
{
    return new Condition(
        name: 'isEnum',
        validator: static fn (ReflectionClass $value): bool => $value->isEnum(),
        message: 'Class "{{ value.name }}" is not an enum',
    );
}

function isFinal(): Condition
{
    return new Condition(
        name: 'isFinal',
        validator: static fn (ReflectionClass $value): bool => $value->isFinal(),
        message: 'Class "{{ value.name }}" is not final',
    );
}

function isInterface(): Condition
{
    return new Condition(
        name: 'isInterface',
        validator: static fn (ReflectionClass $value): bool => $value->isInterface(),
        message: 'Class "{{ value.name }}" is not an interface',
    );
}

function isInstantiable(): Condition
{
    return new Condition(
        name: 'isInstantiable',
        validator: static fn (ReflectionClass $value): bool => $value->isInstantiable(),
        message: 'Class "{{ value.name }}" is not instantiable',
    );
}

function isInternal(): Condition
{
    return new Condition(
        name: 'isInternal',
        validator: static fn (ReflectionClass $value): bool => $value->isInternal(),
        message: 'Class "{{ value.name }}" is not internal',
    );
}

function isInvokable(): Condition
{
    return new Condition(
        name: 'isInvokable',
        validator: static fn (ReflectionClass $value): bool => $value->hasMethod('__invoke'),
        message: 'Class "{{ value.name }}" is not invokable',
    );
}

function isIterable(): Condition
{
    return new Condition(
        name: 'isIterable',
        validator: static fn (ReflectionClass $value): bool => $value->isIterable(),
        message: 'Class "{{ value.name }}" is not iterable',
    );
}

function isIterateable(): Condition
{
    return new Condition(
        name: 'isIterateable',
        validator: static fn (ReflectionClass $value): bool => $value->isIterateable(),
        message: 'Class "{{ value.name }}" is not iterateable',
    );
}

function isReadOnly(): Condition
{
    return new Condition(
        name: 'isReadOnly',
        validator: static fn (ReflectionClass $value): bool => $value->isReadOnly(),
        message: 'Class "{{ value.name }}" is not read-only',
    );
}

function isSubclassOf(string $class): Condition
{
    return new Condition(
        name: 'isSubclassOf',
        validator: static fn (ReflectionClass $value): bool => $value->isSubclassOf($class),
        parameters: ['class' => $class],
        message: 'Class "{{ value.name }}" is not a subclass of "{{ class }}"',
    );
}

function isTrait(): Condition
{
    return new Condition(
        name: 'isTrait',
        validator: static fn (ReflectionClass $value): bool => $value->isTrait(),
        message: 'Class "{{ value.name }}" is not a trait',
    );
}

function isUserDefined(): Condition
{
    return new Condition(
        name: 'isUserDefined',
        validator: static fn (ReflectionClass $value): bool => $value->isUserDefined(),
        message: 'Class "{{ value.name }}" is not user-defined',
    );
}

function matchWith(string $pattern): Condition
{
    return new Condition(
        name: 'matchWith',
        validator: static fn (ReflectionClass $value): bool => preg_match($pattern, $value->getName()) === 1,
        parameters: ['pattern' => $pattern],
        message: 'Class name "{{ value.name }}" does not match pattern "{{ pattern }}"',
    );
}

function residesIn(string $namespace): Condition
{
    return new Condition(
        name: 'residesIn',
        validator: static fn (ReflectionClass $value): bool => $value->getNamespaceName() === $namespace,
        parameters: ['namespace' => $namespace],
        message: 'Class "{{ value.name }}"does not reside in namespace "{{ namespace }}"',
    );
}

function uses(string $trait): Condition
{
    return new Condition(
        name: 'uses',
        validator: static fn (ReflectionClass $value): bool => count(
            array_filter(
                $value->getTraits(),
                static fn (ReflectionClass $class): bool => $class->getName() === $trait
            )
        ) === 1,
        parameters: ['trait' => $trait],
        message: 'Class "{{ value.name }}" does not use trait "{{ trait }}"',
    );
}
