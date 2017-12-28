<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict\InArrayStrictFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Strict\InArrayStrictFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class InArrayStrictFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testFix(string $wrongFile, string $fixedFile): void
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
        return new InArrayStrictFixer();
    }
}
