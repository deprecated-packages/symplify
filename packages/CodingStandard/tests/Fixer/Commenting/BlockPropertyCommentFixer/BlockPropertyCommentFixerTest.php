<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\BlockPropertyCommentFixer;

use Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class BlockPropertyCommentFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc', __DIR__ . '/Fixture/wrong2.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return BlockPropertyCommentFixer::class;
    }
}
