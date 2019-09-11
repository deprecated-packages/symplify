<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveEmptyDocBlockFixer;

use Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class RemoveEmptyDocBlockFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return RemoveEmptyDocBlockFixer::class;
    }
}
