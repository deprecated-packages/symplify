<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\ExceptionNameFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Naming\ExceptionNameFixer;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

final class ExceptionNameFixerTest extends AbstractContainerAwareCheckerTestCase
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
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return $this->container->get(ExceptionNameFixer::class);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
