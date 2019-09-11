<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\TraitName;

use Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class TraitNameSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/correct2.php.inc',
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return TraitNameSniff::class;
    }
}
