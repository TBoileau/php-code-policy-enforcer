<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Validator;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMap;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMapper;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Foo;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\hasDefaultValue;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\hasIntersectionType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\hasNamedType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\hasUnionType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\isPrivate;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\isPromoted;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\isProtected;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\isPublic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\isReadOnly;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\isStatic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Property\matchWith;

final class PropertyValidatorTest extends TestCase
{
    private ClassMap $classMap;

    protected function setUp(): void
    {
        $this->classMap = (new ClassMapper())->add(__DIR__ . '/../Fixtures')->generate();
    }

    #[Test]
    #[DataProvider('provideValidators')]
    public function shouldBeEvaluate(string $class, string $property, ?ConditionalExpression $succeededExpression = null, ?ConditionalExpression $failedExpression = null): void
    {
        if (null === $succeededExpression && null === $failedExpression) {
            self::fail('Missing expression');
        }

        if (null !== $succeededExpression) {
            $report = new Report($this->classMap[$class]->getProperty($property), $succeededExpression);
            self::assertTrue($succeededExpression->evaluate($report));
        }

        if (null !== $failedExpression) {
            $report = new Report($this->classMap[$class]->getProperty($property), $failedExpression);
            self::assertFalse($failedExpression->evaluate($report));
        }
    }

    /**
     * @return Generator<string, array{class-string, string, ?ConditionalExpression, ?ConditionalExpression}>
     */
    public static function provideValidators(): Generator
    {
        yield 'hasIntersectionType' => [Foo::class, 'plugh', null, hasIntersectionType()];
        yield 'hasUnionType' => [Foo::class, 'plugh', null, hasUnionType()];
        yield 'hasNamedType' => [Foo::class, 'plugh', hasNamedType(), null];
        yield 'isPromoted' => [Foo::class, 'plugh', null, isPromoted()];
        yield 'isStatic' => [Foo::class, 'plugh', null, isStatic()];
        yield 'isPrivate' => [Foo::class, 'plugh', null, isPrivate()];
        yield 'isProtected' => [Foo::class, 'plugh', null, isProtected()];
        yield 'isPublic' => [Foo::class, 'plugh', isPublic(), null];
        yield 'hasDefaultValue' => [Foo::class, 'plugh', hasDefaultValue(), null];
        yield 'isReadOnly' => [Foo::class, 'plugh', null, isReadOnly()];
        yield 'matchWith' => [Foo::class, 'plugh', matchWith('/plugh/'), matchWith('/fail/')];
    }
}
