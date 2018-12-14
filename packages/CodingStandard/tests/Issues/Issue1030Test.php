<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1030Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/correct1030.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return ForbiddenStaticFunctionSniff::class;
    }
}
