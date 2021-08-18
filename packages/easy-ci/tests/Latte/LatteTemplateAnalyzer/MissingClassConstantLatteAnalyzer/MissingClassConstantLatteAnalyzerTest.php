<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\MissingClassConstantLatteAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\LatteTemplateAnalyzer\MissingClassConstantLatteAnalyzer;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingClassConstantLatteAnalyzerTest extends AbstractKernelTestCase
{
    private MissingClassConstantLatteAnalyzer $missingClassConstantLatteAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->missingClassConstantLatteAnalyzer = $this->getService(MissingClassConstantLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $templateErrors = $this->missingClassConstantLatteAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $templateErrors);
    }

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_constant.twig'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_number_constant.twig'), 1];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_constant.twig'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/inside_foreach.twig'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_var_type.latte'), 0];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_constant_with_number.twig'), 0];
    }
}
