<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\Analyzer\MissingClassStaticCallLatteAnalyzer;

use Iterator;
use Symplify\TemplateChecker\Analyzer\MissingClassStaticCallLatteAnalyzer;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassStaticCallLatteAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var MissingClassStaticCallLatteAnalyzer
     */
    private $missingClassStaticCallLatteAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->missingClassStaticCallLatteAnalyzer = self::$container->get(MissingClassStaticCallLatteAnalyzer::class);
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

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
