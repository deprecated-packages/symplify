<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassConstantTwigAnalyzerTest extends AbstractKernelTestCase
{
    private MissingClassConstantTwigAnalyzer $missingClassConstantTwigAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->missingClassConstantTwigAnalyzer = $this->getService(MissingClassConstantTwigAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $templateErrors = $this->missingClassConstantTwigAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $templateErrors);
    }

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_constant.twig'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_constant.twig'), 0];
    }
}
