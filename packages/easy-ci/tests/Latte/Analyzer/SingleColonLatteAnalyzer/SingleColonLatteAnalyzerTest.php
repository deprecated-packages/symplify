<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\Analyzer\SingleColonLatteAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\Analyzer\SingleColonLatteAnalyzer;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SingleColonLatteAnalyzerTest extends AbstractKernelTestCase
{
    private SingleColonLatteAnalyzer $singleColonLatteAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->singleColonLatteAnalyzer = $this->getService(SingleColonLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $latteErrors = $this->singleColonLatteAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $latteErrors);
    }

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_simple_text.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_double_colon.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/single_colon.latte'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/single_colon_call.latte'), 1];
    }
}
