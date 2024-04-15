<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Class;

use Countable;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;

function hasAttribute(string $attribute): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasAttribute',
        validator: static fn (ReflectionClass $value): bool => count($value->getAttributes($attribute)) > 0,
        parameters: ['attribute' => $attribute],
        error: 'Class {{ value.name }} does not have attribute {{ parameters.attribute }}',
        message: 'has attribute {{ attribute }}',
    );
}

function hasConstant(string $constant): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasConstant',
        validator: static fn (ReflectionClass $value): bool => $value->hasConstant($constant),
        parameters: ['constant' => $constant],
        error: 'Class {{ value.name }} does not have constant {{ parameters.constant }}',
        message: 'has constant {{ constant }}',
    );
}

function hasMethod(string $method): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasMethod',
        validator: static fn (ReflectionClass $value): bool => $value->hasMethod($method),
        parameters: ['method' => $method],
        error: 'Class {{ value.name }} does not have method {{ parameters.method }}',
        message: 'has method {{ method }}',
    );
}

function hasProperty(string $property): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasProperty',
        validator: static fn (ReflectionClass $value): bool => $value->hasProperty($property),
        parameters: ['property' => $property],
        error: 'Class {{ value.name }} does not have property {{ parameters.property }}',
        message: 'has property {{ property }}',
    );
}

function implementsInterface(string $interface): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'implementsInterface',
        validator: static fn (ReflectionClass $value): bool => $value->implementsInterface($interface),
        parameters: ['interface' => $interface],
        error: 'Class {{ value.name }} does not implement interface {{ parameters.interface }}',
        message: 'implements interface {{ interface }}',
    );
}

function inNamespace(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'inNamespace',
        validator: static fn (ReflectionClass $value): bool => $value->inNamespace(),
        error: 'Class {{ value.name }} is not in a namespace',
        message: 'in a namespace',
    );
}

function isAbstract(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isAbstract',
        validator: static fn (ReflectionClass $value): bool => $value->isAbstract(),
        error: 'Class {{ value.name }} is not abstract',
        message: 'is abstract',
    );
}

function isAnonymous(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isAnonymous',
        validator: static fn (ReflectionClass $value): bool => $value->isAnonymous(),
        error: 'Class {{ value.name }} is not anonymous',
        message: 'is anonymous',
    );
}

function isCloneable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isCloneable',
        validator: static fn (ReflectionClass $value): bool => $value->isCloneable(),
        error: 'Class {{ value.name }} is not cloneable',
        message: 'is cloneable',
    );
}

function isCountable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isCountable',
        validator: static fn (ReflectionClass $value): bool => $value->implementsInterface(Countable::class),
        error: 'Class {{ value.name }} does not implement Countable interface',
        message: 'is countable',
    );
}

function isEnum(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isEnum',
        validator: static fn (ReflectionClass $value): bool => $value->isEnum(),
        error: 'Class {{ value.name }} is not an enum',
        message: 'is an enum',
    );
}

function isFinal(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isFinal',
        validator: static fn (ReflectionClass $value): bool => $value->isFinal(),
        error: 'Class {{ value.name }} is not final',
        message: 'is final',
    );
}

function isInterface(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInterface',
        validator: static fn (ReflectionClass $value): bool => $value->isInterface(),
        error: 'Class {{ value.name }} is not an interface',
        message: 'is an interface',
    );
}

function isInstantiable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInstantiable',
        validator: static fn (ReflectionClass $value): bool => $value->isInstantiable(),
        error: 'Class {{ value.name }} is not instantiable',
        message: 'is instantiable',
    );
}

function isInternal(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInternal',
        validator: static fn (ReflectionClass $value): bool => $value->isInternal(),
        error: 'Class {{ value.name }} is not internal',
        message: 'is internal',
    );
}

function isInvokable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInvokable',
        validator: static fn (ReflectionClass $value): bool => $value->hasMethod('__invoke'),
        error: 'Class {{ value.name }} must implement __invoke method',
        message: 'is invokable',
    );
}

function isIterable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isIterable',
        validator: static fn (ReflectionClass $value): bool => $value->isIterable(),
        error: 'Class {{ value.name }} is not iterable',
        message: 'is iterable',
    );
}

function isIterateable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isIterateable',
        validator: static fn (ReflectionClass $value): bool => $value->isIterateable(),
        error: 'Class {{ value.name }} is not iterateable',
        message: 'is iterateable',
    );
}

function isReadOnly(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isReadOnly',
        validator: static fn (ReflectionClass $value): bool => $value->isReadOnly(),
        error: 'Class {{ value.name }} is not read-only',
        message: 'is read-only',
    );
}

function isSubclassOf(string $class): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isSubclassOf',
        validator: static fn (ReflectionClass $value): bool => $value->isSubclassOf($class),
        parameters: ['class' => $class],
        error: 'Class {{ value.name }} is not a subclass of {{ parameters.class }}',
        message: 'is a subclass of {{ class }}',
    );
}

function isTrait(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isTrait',
        validator: static fn (ReflectionClass $value): bool => $value->isTrait(),
        error: 'Class {{ value.name }} is not a trait',
        message: 'is a trait',
    );
}

function isUserDefined(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isUserDefined',
        validator: static fn (ReflectionClass $value): bool => $value->isUserDefined(),
        error: 'Class {{ value.name }} is not user-defined',
        message: 'is user-defined',
    );
}

function matchWith(string $pattern): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'matchWith',
        validator: static fn (ReflectionClass $value): bool => preg_match($pattern, $value->getName()) === 1,
        parameters: ['pattern' => $pattern],
        error: 'Class name {{ value.name }} does not match pattern {{ parameters.pattern }}',
        message: 'match with pattern {{ pattern }}',
    );
}

function residesIn(string $namespace): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'residesIn',
        validator: static fn (ReflectionClass $value): bool => $value->getNamespaceName() === $namespace,
        parameters: ['namespace' => $namespace],
        error: 'Class {{ value.name }}does not reside in namespace {{ parameters.namespace }}',
        message: 'resides in namespace {{ namespace }}',
    );
}

function uses(string $trait): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'uses',
        validator: static fn (ReflectionClass $value): bool => count(
            array_filter(
                $value->getTraits(),
                static fn (ReflectionClass $class): bool => $class->getName() === $trait
            )
        ) === 1,
        parameters: ['trait' => $trait],
        error: 'Class {{ value.name }} does not use trait {{ parameters.trait }}',
        message: 'uses trait {{ trait }}',
    );
}
