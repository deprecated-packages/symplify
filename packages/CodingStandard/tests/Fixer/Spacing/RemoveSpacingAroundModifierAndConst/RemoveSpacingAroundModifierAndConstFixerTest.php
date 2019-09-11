<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\RemoveSpacingAroundModifierAndConst;

use Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class RemoveSpacingAroundModifierAndConstFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return RemoveSpacingAroundModifierAndConstFixer::class;
    }
}
