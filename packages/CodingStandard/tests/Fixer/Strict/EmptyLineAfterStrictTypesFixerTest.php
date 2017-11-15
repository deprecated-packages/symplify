<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Strict\EmptyLineAfterStrictTypesFixer;

final class EmptyLineAfterStrictTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $input, string $fixedOutput): void
    {
        $this->doTest($fixedOutput, $input);
    }

    /**
     * @return string[][]
     */
    public function provideFixCases(): array
    {
        return [
            [
                // wrong
                '<?php declare(strict_types=1);
namespace SomeNamespace;',
                // fixed
                '<?php declare(strict_types=1);

namespace SomeNamespace;',
            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new EmptyLineAfterStrictTypesFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', PHP_EOL));
        return $fixer;
    }
}
