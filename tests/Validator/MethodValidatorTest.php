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

use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\containsParameters;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasParameter;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasReturnType;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isAbstract;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isPrivate;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isProtected;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isPublic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isStatic;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\matchWith;

final class MethodValidatorTest extends TestCase
{
    private ClassMap $classMap;

    protected function setUp(): void
    {
        $this->classMap = (new ClassMapper())->add(__DIR__ . '/../Fixtures')->generate();
    }

    #[Test]
    #[DataProvider('provideValidators')]
    public function shouldBeEvaluate(string $class, string $method, ?ConditionalExpression $succeededExpression = null, ?ConditionalExpression $failedExpression = null): void
    {
        if (null === $succeededExpression && null === $failedExpression) {
            self::fail('Missing expression');
        }

        if (null !== $succeededExpression) {
            $report = new Report($this->classMap[$class]->getMethod($method), $succeededExpression);
            self::assertTrue($succeededExpression->evaluate($report));
        }

        if (null !== $failedExpression) {
            $report = new Report($this->classMap[$class]->getMethod($method), $failedExpression);
            self::assertFalse($failedExpression->evaluate($report));
        }
    }

    /**
     * @return Generator<string, array{class-string, string, ?ConditionalExpression, ?ConditionalExpression}>
     */
    public static function provideValidators(): Generator
    {
        yield 'containsParameters' => [Foo::class, '__invoke', containsParameters(1), containsParameters(2)];
        yield 'hasParameter' => [Foo::class, '__invoke', hasParameter('bar'), hasParameter('fail')];
        yield 'hasReturnType' => [Foo::class, 'count', hasReturnType(), null];
        yield 'isAbstract' => [Foo::class, '__invoke', null, isAbstract()];
        yield 'isFinal' => [Foo::class, '__invoke', null, isFinal()];
        yield 'isPrivate' => [Foo::class, '__invoke', null, isPrivate()];
        yield 'isProtected' => [Foo::class, '__invoke', null, isProtected()];
        yield 'isPublic' => [Foo::class, '__invoke', isPublic(), null];
        yield 'isStatic' => [Foo::class, '__invoke', null, isStatic()];
        yield 'matchWith' => [Foo::class, '__invoke', matchWith('/invoke/'), matchWith('/fail/')];
    }
}
