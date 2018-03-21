<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\MagicMethodsNamingFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Naming\MagicMethodsNamingFixer;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class MagicMethodsNamingFixerTest extends AbstractContainerAwareCheckerTestCase
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
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return $this->container->get(MagicMethodsNamingFixer::class);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
