<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\StaticDetector\Strings;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\StaticDetector\Strings\StringsFilter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class StringsFilterTest extends AbstractKernelTestCase
{
    private StringsFilter $stringsFilter;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->stringsFilter = $this->getService(StringsFilter::class);
    }

    /**
     * @param string[] $matchingValues
     * @dataProvider provideData()
     */
    public function test(string $inputValue, array $matchingValues, bool $expectedIsMatch): void
    {
        $isMatch = $this->stringsFilter->isMatchOrFnMatch($inputValue, $matchingValues);
        $this->assertSame($expectedIsMatch, $isMatch);
    }

    public function provideData(): Iterator
    {
        yield ['some', [], true];
        yield ['some', ['another'], false];
        yield ['Etra', ['tra'], false];
        // fnmatch
        yield ['Etra', ['*tra'], true];
        yield ['Etra', ['Etr*'], true];
        yield ['Etra\\Large', ['Etr*'], true];
        yield ['Etra\\Large', ['Etr*'], true];
        yield ['Etra\\Large', ['\\Etr*'], false];
    }
}
