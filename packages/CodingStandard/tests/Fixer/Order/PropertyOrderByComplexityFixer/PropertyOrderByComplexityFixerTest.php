<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\PropertyOrderByComplexityFixer;

use Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class PropertyOrderByComplexityFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc', __DIR__ . '/Fixture/wrong2.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return PropertyOrderByComplexityFixer::class;
    }
}
