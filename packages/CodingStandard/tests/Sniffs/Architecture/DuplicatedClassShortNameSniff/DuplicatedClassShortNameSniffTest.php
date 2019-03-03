<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\DuplicatedClassShortNameSniff;

use Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class DuplicatedClassShortNameSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return DuplicatedClassShortNameSniff::class;
    }
}
