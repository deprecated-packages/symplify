<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\BlockPropertyCommentFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class Test extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public function provideFixCases(): array
    {
        return [
            [__DIR__ . '/fixed/fixed.php.inc', __DIR__ . '/wrong/wrong.php.inc', ],
            [__DIR__ . '/fixed/fixed2.php.inc', __DIR__ . '/wrong/wrong2.php.inc', ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $blockPropertyCommentFixer = new BlockPropertyCommentFixer();
        $blockPropertyCommentFixer->setWhitespacesConfig($this->createWhitespacesFixerConfig());

        return $blockPropertyCommentFixer;
    }

    private function createWhitespacesFixerConfig(): WhitespacesFixerConfig
    {
        // @try tabs?
        return new WhitespacesFixerConfig('    ', PHP_EOL);
    }
}
