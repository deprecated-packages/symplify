<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer
 */
final class ExistenceNotRequiredTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong4.php.inc', __DIR__ . '/Fixture/wrong5.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-with-non-existance.yml';
    }
}
