<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue972Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong972.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return BlockPropertyCommentFixer::class;
    }
}
