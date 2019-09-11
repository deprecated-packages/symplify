<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;

use Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class RequireFollowedByAbsolutePathFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong_with_double_quotes.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return RequireFollowedByAbsolutePathFixer::class;
    }
}
