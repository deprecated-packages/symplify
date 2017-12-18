<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ConfiguredTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrectCases(string $file): void
    {
        $this->doTestCorrectFile($file);
    }

    /**
     * @return string[][]
     */
    public function provideCorrectCases(): array
    {
        return [
            [__DIR__ . '/correct/correct10.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new RemoveUselessDocBlockFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());
        $fixer->configure([
           'useful_types' => ['mixed', 'object'],
        ]);

        return $fixer;
    }
}
