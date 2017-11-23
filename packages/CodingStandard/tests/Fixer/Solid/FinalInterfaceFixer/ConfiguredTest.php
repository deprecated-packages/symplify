<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ConfiguredTest extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new FinalInterfaceFixer();

        $fixer->configure([
            'only_interfaces' => ['SomeInterface'],
        ]);

        return $fixer;
    }
}
