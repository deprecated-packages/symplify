<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;

final class ConfiguredTest extends AbstractFixerTestCase
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
            [
                file_get_contents(__DIR__ . '/fixed/fixed3.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong3.php.inc'),
            ],
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
