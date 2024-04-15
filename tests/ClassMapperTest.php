<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\Reflection\ReflectionClass;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMapper;

final class ClassMapperTest extends TestCase
{
    #[Test]
    public function shouldGenerateClassMap(): void
    {
        $classMap = ClassMapper::generateClassMap(__DIR__ . '/Fixtures');

        self::assertCount(6, $classMap);
        self::assertContainsOnlyInstancesOf(ReflectionClass::class, $classMap);
    }
}
