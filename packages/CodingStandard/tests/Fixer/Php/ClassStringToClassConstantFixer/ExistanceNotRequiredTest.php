<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ExistanceNotRequiredTest extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/fixed/fixed4.php.inc', __DIR__ . '/wrong/wrong4.php.inc', ],
            [__DIR__ . '/fixed/fixed5.php.inc', __DIR__ . '/wrong/wrong5.php.inc', ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $classStringToClassConstantFixer = new ClassStringToClassConstantFixer();
        $classStringToClassConstantFixer->configure([
            'class_must_exist' => false,
        ]);

        return $classStringToClassConstantFixer;
    }
}
