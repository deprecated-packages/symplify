<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\MissingClassStaticCallLatteAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\LatteTemplateAnalyzer\MissingClassStaticCallLatteAnalyzer;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassStaticCallLatteAnalyzerTest extends AbstractKernelTestCase
{
    private MissingClassStaticCallLatteAnalyzer $missingClassStaticCallLatteAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->missingClassStaticCallLatteAnalyzer = $this->getService(MissingClassStaticCallLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpected($fixtureFileInfo);
        $expectedErrorCount = (int) $inputFileInfoAndExpected->getExpected();

        $errorMessages = $this->missingClassStaticCallLatteAnalyzer->analyze(
            [$inputFileInfoAndExpected->getInputFileInfo()]
        );

        $this->assertCount($expectedErrorCount, $errorMessages, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
