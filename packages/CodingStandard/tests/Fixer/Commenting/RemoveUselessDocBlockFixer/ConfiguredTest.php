<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

/**
 * @covers \Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer
 */
final class ConfiguredTest extends AbstractContainerAwareCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testFix(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function provideWrongToFixedCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong13.php.inc', __DIR__ . '/fixed/fixed13.php.inc'];
        yield [__DIR__ . '/wrong/wrong14.php.inc', __DIR__ . '/fixed/fixed14.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-configured.yml';
    }
}
