<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Validator;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Foo;

use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Quux;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isReadOnly;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\isOptional;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\isPassedByReference;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\isVariadic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\hasDefaultValue;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\hasIntersectionType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\hasNamedType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\hasUnionType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Parameter\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\is;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isArray;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isBoolean;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isBuiltIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isCallable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isClass;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isFalse;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isFloat;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isInteger;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isIterable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isMixed;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isNever;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isNull;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isObject;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isParent;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isSelf;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isStatic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isString;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isTrue;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Type\isVoid;

final class ParameterValidatorTest extends TestCase
{
    #[Test]
    #[DataProvider('provideValidators')]
    public function shouldBeEvaluate(string $class, string $method, string $parameter, ?ConditionalExpression $succeededExpression = null, ?ConditionalExpression $failedExpression = null): void
    {
        if (null === $succeededExpression && null === $failedExpression) {
            self::fail('Missing expression');
        }

        $parameter = new ReflectionParameter([$class, $method], $parameter);

        if (null !== $succeededExpression) {
            $report = new Report($parameter, $succeededExpression);
            self::assertTrue($succeededExpression->evaluate($report));
        }

        if (null !== $failedExpression) {
            $report = new Report($parameter, $failedExpression);
            self::assertFalse($failedExpression->evaluate($report));
        }
    }

    /**
     * @return Generator<string, array{class-string, string, ?ConditionalExpression, ?ConditionalExpression}>
     */
    public static function provideValidators(): Generator
    {
        yield 'hasNamedType' => [Foo::class, 'fred', 'quux', hasNamedType(
            andX(
                not(isBuiltIn()),
                not(isArray()),
                not(isBoolean()),
                not(isCallable()),
                not(isFloat()),
                not(isInteger()),
                not(isNull()),
                not(isObject()),
                not(isString()),
                not(isFalse()),
                not(isIterable()),
                not(isMixed()),
                not(isNever()),
                not(isTrue()),
                not(isVoid()),
                not(isParent()),
                not(isSelf()),
                not(isStatic()),
                isClass(Quux::class),
                is(isReadOnly())
            )
        ), null];
        yield 'hasIntersectionType' => [Foo::class, 'fred', 'quux', null, hasIntersectionType()];
        yield 'hasUnionType' => [Foo::class, 'fred', 'quux', null, hasUnionType()];
        yield 'hasDefaultValue' => [Foo::class, 'fred', 'quux', null, hasDefaultValue()];
        yield 'isPassedByReference' => [Foo::class, 'fred', 'quux', null, isPassedByReference()];
        yield 'isVariadic' => [Foo::class, 'fred', 'quux', null, isVariadic()];
        yield 'isOptional' => [Foo::class, 'fred', 'quux', null, isOptional()];
        yield 'matchWith' => [Foo::class, 'fred', 'quux', matchWith('/quux/'), matchWith('/fail/')];
    }
}
