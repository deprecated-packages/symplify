<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\ExplicitExceptionSniff;

use Symplify\CodingStandard\Sniffs\Architecture\ExplicitExceptionSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ExplicitExceptionSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/correct.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return ExplicitExceptionSniff::class;
    }
}
