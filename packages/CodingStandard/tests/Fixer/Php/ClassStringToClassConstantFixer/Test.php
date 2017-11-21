<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class Test extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc', ],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc', ],
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc', ],
            # correct
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
