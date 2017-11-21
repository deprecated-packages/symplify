<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class BlankLineAfterStrictTypesFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public function provideFixCases(): array
    {
        return [
            # wrong => fixed
            [
                '<?php declare(strict_types=1);
namespace SomeNamespace;',
                '<?php declare(strict_types=1);

namespace SomeNamespace;',
            ], [
                '<?php declare(strict_types=1);


namespace SomeNamespace;',
                '<?php declare(strict_types=1);

namespace SomeNamespace;',
            ],
            # correct
            ['<?php declare(strict_types=1);
', ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new BlankLineAfterStrictTypesFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', PHP_EOL));
        return $fixer;
    }
}
