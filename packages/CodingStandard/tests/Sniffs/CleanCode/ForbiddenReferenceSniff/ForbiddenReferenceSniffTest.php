<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\ForbiddenReferenceSniff;

use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenReferenceSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenReferenceSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/wrong/function_with_space.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return ForbiddenReferenceSniff::class;
    }
}
