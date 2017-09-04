<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ClassNotation\PropertyAndConstantSeparationFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\ClassNotation\PropertyAndConstantSeparationFixer;

final class Test extends AbstractFixerTestCase
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
                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong.php.inc'),
            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed3.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong3.php.inc'),
            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $propertyAndConstantSeparationFixer = new PropertyAndConstantSeparationFixer;
        $propertyAndConstantSeparationFixer->setWhitespacesConfig($this->createWhitespacesFixerConfig());

        return $propertyAndConstantSeparationFixer;
    }

    private function createWhitespacesFixerConfig(): WhitespacesFixerConfig
    {
        return new WhitespacesFixerConfig('    ', PHP_EOL);
    }
}
