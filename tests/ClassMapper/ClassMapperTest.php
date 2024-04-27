<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Tests\ClassMapper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TBoileau\PhpCodePolicyEnforcer\ClassMapper\ClassMapper;
use TBoileau\PhpCodePolicyEnforcer\Exception\ClassMapperException;

#[CoversClass(ClassMapper::class)]
final class ClassMapperTest extends TestCase
{
    public const FIXTURES_DIR = __DIR__ . '/../Fixtures';

    private ClassMapper $classMapper;

    protected function setUp(): void
    {
        $this->classMapper = new ClassMapper();
    }

    #[Test]
    public function shouldGenerateClassMap(): void
    {
        $classMap = $this->classMapper->add(self::FIXTURES_DIR)->generate();
        self::assertCount(1, $this->classMapper->getDirectories());
        self::assertEquals('/tests/Fixtures', $this->classMapper->getDirectories()[0]);
        self::assertCount(8, $classMap);
    }

    #[Test]
    public function thatNonExistingDirectoryShouldRaiseAnException(): void
    {
        self::expectException(ClassMapperException::class);
        self::expectExceptionMessage('"fail" directory does not exist.');
        $this->classMapper->add('fail');
        self::assertCount(1, $this->classMapper->getDirectories());
    }
}
