<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue973Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/correct973.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return LineLengthFixer::class;
    }
}
