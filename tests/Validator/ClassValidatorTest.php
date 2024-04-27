<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\Validator;

use ArrayAccess;
use Attribute;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMap;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMapper;
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;
use TBoileau\PhpCodePolicyEnforcer\Report\Report;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Bar;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Baz;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Corge;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Foo;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault\Garply;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault\Qux;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Quux;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Xyzzy;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\dependsOn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasAttribute;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasConstant;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasConstructor;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasProperty;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\implementsInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\inNamespace;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isAnonymous;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isCloneable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isCountable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInstantiable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInternal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInvokable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isIterable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isIterateable;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isReadOnly;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isSubclassOf;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isTrait;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isUserDefined;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\matchWith;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\uses;

final class ClassValidatorTest extends TestCase
{
    private ClassMap $classMap;

    protected function setUp(): void
    {
        $this->classMap = (new ClassMapper())->add(__DIR__ . '/../Fixtures')->generate();
    }

    #[Test]
    #[DataProvider('provideValidators')]
    public function shouldBeEvaluate(string $class, ?ConditionalExpression $succeededExpression = null, ?ConditionalExpression $failedExpression = null): void
    {
        if (null === $succeededExpression && null === $failedExpression) {
            self::fail('Missing expression');
        }

        if (null !== $succeededExpression) {
            $report = new Report($this->classMap[$class], $succeededExpression);
            self::assertTrue($succeededExpression->evaluate($report));
        }

        if (null !== $failedExpression) {
            $report = new Report($this->classMap[$class], $failedExpression);
            self::assertFalse($failedExpression->evaluate($report));
        }
    }

    /**
     * @return Generator<string, array{class-string, ?ConditionalExpression, ?ConditionalExpression}>
     */
    public static function provideValidators(): Generator
    {
        yield 'dependsOn' => [Foo::class, dependsOn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Grault', '\\'), dependsOn(Garply::class)];
        yield 'hasAttribute' => [Foo::class, hasAttribute(Attribute::class), hasAttribute(Garply::class)];
        yield 'hasConstant' => [Foo::class, hasConstant('WALDO'), hasConstant('FAIL')];
        yield 'hasMethod' => [Foo::class, hasMethod('fred'), hasMethod('fail')];
        yield 'hasProperty' => [Foo::class, hasProperty('plugh'), hasProperty('fail')];
        yield 'implementsInterface' => [Foo::class, implementsInterface(Bar::class), implementsInterface(ArrayAccess::class)];
        yield 'isSubclassOf' => [Foo::class, isSubclassOf(Qux::class), isSubclassOf(stdClass::class)];
        yield 'matchWith' => [Foo::class, matchWith('/[a-zA-Z]+/'), matchWith('/fail/')];
        yield 'residesIn' => [Foo::class, residesIn('TBoileau\PhpCodePolicyEnforcer\Tests'), residesIn('TBoileau\PhpCodePolicyEnforcer\Lib')];
        yield 'uses' => [Foo::class, uses(Corge::class), uses(Xyzzy::class)];
        yield 'inNamespace' => [Foo::class, inNamespace(), null];
        yield 'isAbstract' => [Qux::class, isAbstract(), null];
        yield 'isCloneable' => [Foo::class, isCloneable(), null];
        yield 'isCountable' => [Foo::class, isCountable(), null];
        yield 'isEnum' => [Baz::class, isEnum(), null];
        yield 'isFinal' => [Foo::class, isFinal(), null];
        yield 'isInterface' => [Bar::class, isInterface(), null];
        yield 'isInstantiable' => [Foo::class, isInstantiable(), null];
        yield 'isInternal' => [Foo::class, null, isInternal()];
        yield 'isInvokable' => [Foo::class, isInvokable(), null];
        yield 'isIterable' => [Foo::class, isIterable(), null];
        yield 'isIterateable' => [Foo::class, isIterateable(), null];
        yield 'isReadOnly' => [Quux::class, isReadOnly(), null];
        yield 'isTrait' => [Xyzzy::class, isTrait(), null];
        yield 'isUserDefined' => [Foo::class, isUserDefined(), null];
        yield 'isAnonymous' => [Foo::class, null, isAnonymous()];
        yield 'hasConstructor' => [Foo::class, null, hasConstructor()];
    }
}
