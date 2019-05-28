<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\CatchExceptionNameMatchingTypeFixer;

use Symplify\CodingStandard\Fixer\Naming\CatchExceptionNameMatchingTypeFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class CatchExceptionNameMatchingTypeFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/correct.php.inc', __DIR__ . '/Fixture/wrong_to_fixed.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return CatchExceptionNameMatchingTypeFixer::class;
    }
}
