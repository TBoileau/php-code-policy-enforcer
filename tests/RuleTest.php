<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;
use TBoileau\PhpCodePolicyEnforcer\Templating\Templating;
use TBoileau\PhpCodePolicyEnforcer\Templating\TwigTemplating;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Bar;
use TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures\Xyzzy;

use function TBoileau\PhpCodePolicyEnforcer\Lib\Helpers\that;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\andX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\not;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\orX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Operators\Logical\xorX;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\containsMethods;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\hasMethod;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\implementsInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isEnum;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isFinal;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\isInterface;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\residesIn;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Class\uses;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\containsParameters;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\hasParameter;
use function TBoileau\PhpCodePolicyEnforcer\Lib\Validator\Method\isAbstract;

final class RuleTest extends TestCase
{
    private Templating $templating;

    protected function setUp(): void
    {
        $this->templating = new TwigTemplating('text');
    }

    #[Test]
    public function thatSimpleRuleShouldRenderMessage(): void
    {
        $codePolicy = CodePolicy::in(__DIR__ . '/Fixtures');

        $rule = that(residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures'))
            ->should(hasMethod('test'))
            ->because('this is a test')
            ->setCodePolicy($codePolicy);

        self::assertSame(
            file_get_contents(__DIR__ . '/Fixtures/rules/simple_rule.txt'),
            $this->templating->render('rule.twig', ['rule' => $rule])
        );
    }


    #[Test]
    public function thatComplexRuleShouldRenderMessage(): void
    {
        $codePolicy = CodePolicy::in(__DIR__ . '/Fixtures', __DIR__ . '/../src');

        $rule = that(
            residesIn('TBoileau\PhpCodePolicyEnforcer\Tests\Fixtures', 'TBoileau\PhpCodePolicyEnforcer\Cli'),
            orX(
                containsMethods(5),
                not(isFinal()),
                andX(
                    isEnum(),
                    uses(Xyzzy::class),
                    not(implementsInterface(Bar::class))
                )
            )
        )
            ->should(
                hasMethod('test'),
                orX(
                    not(isAbstract()),
                    xorX(
                        not(isInterface()),
                        hasMethod(
                            'test',
                            andX(
                                containsParameters(2),
                                hasParameter('foo'),
                                orX(
                                    hasParameter('bar'),
                                    hasParameter('baz')
                                )
                            )
                        )
                    )
                )
            )
            ->because('this is a test')
            ->setCodePolicy($codePolicy);

        self::assertSame(
            file_get_contents(__DIR__ . '/Fixtures/rules/complex_rule.txt'),
            $this->templating->render('rule.twig', ['rule' => $rule])
        );
    }
}
