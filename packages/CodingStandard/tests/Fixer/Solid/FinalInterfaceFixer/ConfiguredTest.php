<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ConfiguredTest extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new FinalInterfaceFixer();

        $fixer->configure([
            FinalInterfaceFixer::ONLY_INTERFACES_OPTION => ['SomeInterface'],
        ]);

        return $fixer;
    }
}
