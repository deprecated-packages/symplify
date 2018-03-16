<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\BlockPropertyCommentFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

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

    protected function createFixer(): FixerInterface
    {
        return $this->container->get(BlockPropertyCommentFixer::class);
//        $fixer = new BlockPropertyCommentFixer();
//        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', PHP_EOL));
//
//        return $fixer;
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
