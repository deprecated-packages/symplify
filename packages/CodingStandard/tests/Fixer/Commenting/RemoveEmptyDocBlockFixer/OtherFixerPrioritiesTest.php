<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveEmptyDocBlockFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer
 */
final class OtherFixerPrioritiesTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong4.php.inc',
            __DIR__ . '/Fixture/wrong5.php.inc',
            __DIR__ . '/Fixture/wrong6.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/priorities-config.yml';
    }
}
