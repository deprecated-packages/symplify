<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Composer;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Composer\Psr4PathValidator;
use Symplify\PHPStanRules\ValueObject\ClassNamespaceAndDirectory;

final class Psr4PathValidatorTest extends TestCase
{
    private Psr4PathValidator $psr4PathValidator;

    protected function setUp(): void
    {
        $this->psr4PathValidator = new Psr4PathValidator();
    }

    /**
     * @dataProvider provideCorrectData()
     */
    public function testCorrectCase(string $namespace, string $directory): void
    {
        $isClassNamespaceCorrect = $this->isNamespaceAndDirectoryCorrect($namespace, $directory);

        $this->assertTrue($isClassNamespaceCorrect);
    }

    /**
     * @dataProvider provideFailingData()
     */
    public function testFailingCase(string $namespace, string $directory): void
    {
        $isClassNamespaceCorrect = $this->isNamespaceAndDirectoryCorrect($namespace, $directory);

        $this->assertFalse($isClassNamespaceCorrect);
    }

    /**
     * @return Iterator<string[]>
     */
    public function provideCorrectData(): Iterator
    {
        yield ['Symplify\\PHPStanRules\\Tests\\', 'packages/phpstan-rules/tests'];
        yield ['Symplify\\PHPStanRules\\Tests\\', 'packages/phpstan-rules/tests/'];
    }

    public function provideFailingData(): Iterator
    {
        yield ['Symplify\\PHPStanRules\\Tests\\', 'packages/tests/'];
        yield ['Symplify\\PHPStanRules\\Tests\\', 'packages/phpstan-rules/'];
        yield ['PHPStanRules\\Tests', 'packages/tests/'];
    }

    private function isNamespaceAndDirectoryCorrect(string $namespace, string $directory): bool
    {
        $classNamespaceAndDirectory = new ClassNamespaceAndDirectory(
            $namespace,
            $directory,
            sprintf('%sComposer\\', $namespace)
        );

        return $this->psr4PathValidator->isClassNamespaceCorrect($classNamespaceAndDirectory, __FILE__);
    }
}
