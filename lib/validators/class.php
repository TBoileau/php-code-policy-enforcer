<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class;

use Countable;
use TBoileau\PhpCodePolicyEnforcer\Exception\ExpressionException;
use TBoileau\PhpCodePolicyEnforcer\Expression\Expression;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;

use function Symfony\Component\String\u;

function containsMethods(int $numberOfMethods): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'containsMethods',
        validator: static fn (ReflectionClass $value): bool => count($value->getMethods()) === $numberOfMethods,
        parameters: ['numberOfMethods' => $numberOfMethods],
        message: 'contains "{{ numberOfMethods }}" {{ "method"|inflect(numberOfMethods) }}',
    );
}

function containsProperties(int $numberOfProperties): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'containsProperties',
        validator: static fn (ReflectionClass $value): bool => count($value->getProperties()) === $numberOfProperties,
        parameters: ['numberOfProperties' => $numberOfProperties],
        message: 'contains "{{ numberOfProperties }}" {{ "method"|inflect(numberOfProperties) }}',
    );
}

/**
 * @throws ExpressionException
 */
function dependsOn(string ...$namespaces): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'dependsOn',
        validator: static function (ReflectionClass $value) use ($namespaces): bool {
            $count = count($value->getImports());

            foreach ($value->getImports() as $import) {
                foreach ($namespaces as $namespace) {
                    if (
                        ($namespace === '\\' && !str_contains($import->getName(), '\\'))
                        || str_starts_with($import->getName(), $namespace)
                    ) {
                        --$count;
                        continue 2;
                    }
                }
            }

            return $count === 0;
        },
        parameters: ['namespaces' => $namespaces],
        message: 'depends on {{ namespaces|quote|join(", ", " or ")|raw }}',
    );
}

function methods(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'methods',
        validator: static fn (ReflectionClass $value): bool => true,
        message: 'has methods',
        childExpression: $expression,
        childValues: static fn (ReflectionClass $value): array => $value->getMethods()
    );
}

function properties(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'properties',
        validator: static fn (ReflectionClass $value): bool => true,
        message: 'has properties',
        childExpression: $expression,
        childValues: static fn (ReflectionClass $value): array => $value->getProperties()
    );
}

function hasAttribute(string $attribute): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasAttribute',
        validator: static fn (ReflectionClass $value): bool => count($value->getAttributes($attribute)) > 0,
        parameters: ['attribute' => $attribute],
        message: 'has and attribute named "{{ attribute }}"',
    );
}

function hasConstant(string $constant): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasConstant',
        validator: static fn (ReflectionClass $value): bool => $value->hasConstant($constant),
        parameters: ['constant' => $constant],
        message: 'has a constant name "{{ constant }}"',
    );
}

function hasMethod(string $method, ?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasMethod',
        validator: static fn (ReflectionClass $value): bool => $value->hasMethod($method),
        parameters: ['method' => $method],
        message: 'has a method named "{{ method }}"',
        childExpression: $expression,
        childValues: static fn (ReflectionClass $value): array => $value->hasMethod($method)
            ? [$value->getMethod($method)]
            : []
    );
}

function hasProperty(string $property, ?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasProperty',
        validator: static fn (ReflectionClass $value): bool => $value->hasProperty($property),
        parameters: ['property' => $property],
        message: 'has property "{{ property }}"',
        childExpression: $expression,
        childValues: static fn (ReflectionClass $value): array => $value->hasProperty($property)
            ? [$value->getProperty($property)]
            : []
    );
}

function hasConstructor(?Expression $expression = null): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'hasConstructor',
        validator: static fn (ReflectionClass $value): bool => $value->getConstructor() !== null,
        parameters: [],
        message: 'has "constructor"',
        childExpression: $expression,
        childValues: static fn (ReflectionClass $value): array => $value->getConstructor() !== null
            ? [$value->getConstructor()]
            : []
    );
}

function implementsInterface(string $interface): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'implementsInterface',
        validator: static fn (ReflectionClass $value): bool => $value->implementsInterface($interface),
        parameters: ['interface' => $interface],
        message: 'implements interface "{{ interface }}"',
    );
}

function inNamespace(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'inNamespace',
        validator: static fn (ReflectionClass $value): bool => $value->inNamespace(),
        message: 'in a "namespace"',
    );
}

function isAbstract(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isAbstract',
        validator: static fn (ReflectionClass $value): bool => $value->isAbstract(),
        message: 'is "abstract"',
    );
}

function isAnonymous(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isAnonymous',
        validator: static fn (ReflectionClass $value): bool => $value->isAnonymous(),
        message: 'is "anonymous"',
    );
}

function isCloneable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isCloneable',
        validator: static fn (ReflectionClass $value): bool => $value->isCloneable(),
        message: 'is "cloneable"',
    );
}

function isCountable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isCountable',
        validator: static fn (ReflectionClass $value): bool => $value->implementsInterface(Countable::class),
        message: 'is "countable"',
    );
}

function isEnum(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isEnum',
        validator: static fn (ReflectionClass $value): bool => $value->isEnum(),
        message: 'is an "enum"',
    );
}

function isFinal(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isFinal',
        validator: static fn (ReflectionClass $value): bool => $value->isFinal(),
        message: 'is "final"',
    );
}

function isInterface(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInterface',
        validator: static fn (ReflectionClass $value): bool => $value->isInterface(),
        message: 'is an "interface"',
    );
}

function isInstantiable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInstantiable',
        validator: static fn (ReflectionClass $value): bool => $value->isInstantiable(),
        message: 'is "instantiable"',
    );
}

function isInternal(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInternal',
        validator: static fn (ReflectionClass $value): bool => $value->isInternal(),
        message: 'is "internal"',
    );
}

function isInvokable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isInvokable',
        validator: static fn (ReflectionClass $value): bool => $value->hasMethod('__invoke'),
        message: 'is "invokable"',
    );
}

function isIterable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isIterable',
        validator: static fn (ReflectionClass $value): bool => $value->isIterable(),
        message: 'is "iterable"',
    );
}

function isIterateable(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isIterateable',
        validator: static fn (ReflectionClass $value): bool => $value->isIterateable(),
        message: 'is "iterateable"',
    );
}

function isReadOnly(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isReadOnly',
        validator: static fn (ReflectionClass $value): bool => $value->isReadOnly(),
        message: 'is "read-only"',
    );
}

function isSubclassOf(string $class): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isSubclassOf',
        validator: static fn (ReflectionClass $value): bool => $value->isSubclassOf($class),
        parameters: ['class' => $class],
        message: 'is a subclass of "{{ class }}"',
    );
}

function isTrait(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isTrait',
        validator: static fn (ReflectionClass $value): bool => $value->isTrait(),
        message: 'is a "trait"',
    );
}

function isUserDefined(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'isUserDefined',
        validator: static fn (ReflectionClass $value): bool => $value->isUserDefined(),
        message: 'is "user-defined"',
    );
}

function matchWith(string $pattern): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'matchWith',
        validator: static fn (ReflectionClass $value): bool => preg_match($pattern, $value->getName()) === 1,
        parameters: ['pattern' => $pattern],
        message: 'matches with pattern "{{ pattern }}"',
    );
}

function residesIn(string ...$namespaces): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'residesIn',
        validator: static fn (ReflectionClass $value): bool => u($value->getName())->containsAny($namespaces),
        parameters: ['namespaces' => $namespaces],
        message: 'resides in {{ "namespace"|inflect(namespaces) }} {{ namespaces|quote|join(", ", " or ")|raw }}',
    );
}

function uses(string $trait): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'uses',
        validator: static fn (ReflectionClass $value): bool => count(
            array_filter(
                $value->getTraits(),
                static fn (\ReflectionClass $class): bool => $class->getName() === $trait
            )
        ) === 1,
        parameters: ['trait' => $trait],
        message: 'uses trait "{{ trait }}"',
    );
}
