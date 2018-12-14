<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\AnnotationTypeExistsSniff;

use Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class AnnotationTypeExistsSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return AnnotationTypeExistsSniff::class;
    }
}
