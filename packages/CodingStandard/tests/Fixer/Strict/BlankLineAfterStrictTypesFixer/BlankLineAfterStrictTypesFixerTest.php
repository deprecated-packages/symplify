<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict\BlankLineAfterStrictTypesFixer;

use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class BlankLineAfterStrictTypesFixerTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return BlankLineAfterStrictTypesFixer::class;
    }
}
