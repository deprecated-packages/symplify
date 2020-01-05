<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;

use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @mimic https://github.com/rectorphp/rector/pull/807/files
 * @see \Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer
 */
final class ParamReturnAndVarTagMalformsFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestCorrectFiles([__DIR__ . '/Fixture/correct.php.inc', __DIR__ . '/Fixture/correct2.php.inc']);

        $this->doTestWrongToFixedFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
            __DIR__ . '/Fixture/wrong4.php.inc',
            __DIR__ . '/Fixture/wrong5.php.inc',
            __DIR__ . '/Fixture/wrong6.php.inc',
            __DIR__ . '/Fixture/wrong7.php.inc',
            __DIR__ . '/Fixture/wrong8.php.inc',
            __DIR__ . '/Fixture/wrong9.php.inc',
            __DIR__ . '/Fixture/wrong10.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return ParamReturnAndVarTagMalformsFixer::class;
    }
}
