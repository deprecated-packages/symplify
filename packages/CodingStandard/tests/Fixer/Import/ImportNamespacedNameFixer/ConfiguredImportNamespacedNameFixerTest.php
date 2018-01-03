<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Import\ImportNamespacedNameFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ConfiguredImportNamespacedNameFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong11.php.inc', __DIR__ . '/fixed/fixed11.php.inc'],
            [__DIR__ . '/wrong/wrong12.php.inc', __DIR__ . '/fixed/fixed12.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new ImportNamespacedNameFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());
        $fixer->configure([
            ImportNamespacedNameFixer::INCLUDE_DOC_BLOCKS_OPTION => true,
            ImportNamespacedNameFixer::ALLOW_SINGLE_NAMES_OPTION => true,
        ]);

        return $fixer;
    }
}
