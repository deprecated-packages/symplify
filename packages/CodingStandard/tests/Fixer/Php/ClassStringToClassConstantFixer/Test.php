<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;

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
            ], [
                file_get_contents(__DIR__ . '/fixed/fixed2.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
            ], [
                file_get_contents(__DIR__ . '/fixed/fixed3.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong3.php.inc'),
            ],
            ['<?php $form->addText(\'datetime\');'],
            ['<?php $request->getParameter(\'exception\');'],
            ['<?php $this->assertTrue(class_exists(\'\ApiGen\Reflection\Tests\ExtendingClass\'));'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new ClassStringToClassConstantFixer();
    }
}
