<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\ClassNameSuffixByParentFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Naming\ClassNameSuffixByParentFixer;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ClassNameSuffixByParentFixerTest extends AbstractContainerAwareCheckerTestCase
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
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'],
            [__DIR__ . '/wrong/wrong4.php.inc', __DIR__ . '/fixed/fixed4.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return $this->container->get(ClassNameSuffixByParentFixer::class);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
