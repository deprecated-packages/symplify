<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

final class ExistenceNotRequiredTest extends AbstractContainerAwareCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong4.php.inc', __DIR__ . '/fixed/fixed4.php.inc'],
            [__DIR__ . '/wrong/wrong5.php.inc', __DIR__ . '/fixed/fixed5.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return $this->container->get(ClassStringToClassConstantFixer::class);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-with-non-existance.yml';
    }
}
