<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer
 */
final class AllowedClassesTest extends AbstractCheckerTestCase
{
    public function testWrongToFixed(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct2.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-with-allowed-classes.yml';
    }
}
