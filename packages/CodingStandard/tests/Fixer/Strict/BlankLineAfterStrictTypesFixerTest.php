<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;

final class BlankLineAfterStrictTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $input, ?string $fixedOutput = null): void
    {
        if ($fixedOutput === null) {
            [$input, $fixedOutput] = [$fixedOutput, $input];
        }

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
            ], [
                // wrong
                '<?php declare(strict_types=1);


namespace SomeNamespace;',
                // fixed
                '<?php declare(strict_types=1);

namespace SomeNamespace;',
            ], [
                // correct
                '<?php declare(strict_types=1);
',
            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new BlankLineAfterStrictTypesFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', PHP_EOL));
        return $fixer;
    }
}
