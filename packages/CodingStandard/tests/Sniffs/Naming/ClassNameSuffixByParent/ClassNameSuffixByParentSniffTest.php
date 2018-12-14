<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ClassNameSuffixByParent;

use Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ClassNameSuffixByParentSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/correct2.php.inc',
            __DIR__ . '/Fixture/correct3.php.inc',
            __DIR__ . '/Fixture/correct4.php.inc',
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
            __DIR__ . '/Fixture/wrong4.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return ClassNameSuffixByParentSniff::class;
    }
}
