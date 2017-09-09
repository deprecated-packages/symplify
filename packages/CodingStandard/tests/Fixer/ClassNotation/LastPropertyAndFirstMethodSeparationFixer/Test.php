<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ClassNotation\LastPropertyAndFirstMethodSeparationFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\ClassNotation\LastPropertyAndFirstMethodSeparationFixer;

final class Test extends AbstractFixerTestCase
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
            [
                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong.php.inc'),
            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new LastPropertyAndFirstMethodSeparationFixer;
        $fixer->setWhitespacesConfig($this->createWhitespacesFixerConfig());

        return $fixer;
    }

    private function createWhitespacesFixerConfig(): WhitespacesFixerConfig
    {
        return new WhitespacesFixerConfig('    ', PHP_EOL);
    }
}
