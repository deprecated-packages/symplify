<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @mimic https://github.com/rectorphp/rector/pull/807/files
 */
final class ParamReturnAndVarTagMalformsFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture');
    }

    protected function getCheckerClass(): string
    {
        return ParamReturnAndVarTagMalformsFixer::class;
    }
}
