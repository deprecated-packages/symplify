<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ConfiguredTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testFix(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong13.php.inc', __DIR__ . '/fixed/fixed13.php.inc'],
            [__DIR__ . '/wrong/wrong14.php.inc', __DIR__ . '/fixed/fixed14.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new RemoveUselessDocBlockFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());
        $fixer->configure([
           'useless_types' => ['object', 'mixed'],
        ]);

        return $fixer;
    }
}
