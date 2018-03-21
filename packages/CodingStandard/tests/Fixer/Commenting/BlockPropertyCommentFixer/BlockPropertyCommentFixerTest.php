<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\BlockPropertyCommentFixer;

use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

/**
 * @covers \Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer
 */
final class BlockPropertyCommentFixerTest extends AbstractContainerAwareCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixedCases(string $wrongFile, string $correctFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $correctFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
        ];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
