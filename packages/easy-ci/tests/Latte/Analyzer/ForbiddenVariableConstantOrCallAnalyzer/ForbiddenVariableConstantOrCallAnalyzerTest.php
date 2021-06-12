<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\Analyzer\ForbiddenVariableConstantOrCallAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\Analyzer\ForbiddenVariableConstantOrCallAnalyzer;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ForbiddenVariableConstantOrCallAnalyzerTest extends AbstractKernelTestCase
{
    private ForbiddenVariableConstantOrCallAnalyzer $forbiddenVariableConstantOrCallAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);

        $this->forbiddenVariableConstantOrCallAnalyzer = $this->getService(
            ForbiddenVariableConstantOrCallAnalyzer::class
        );
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo, int $expectedErrorCount): void
    {
        $latteErrors = $this->forbiddenVariableConstantOrCallAnalyzer->analyze([$fileInfo]);
        $this->assertCount($expectedErrorCount, $latteErrors);
    }

    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/some_file_with_expr.latte'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/correct_file.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/constant_with_underscore.latte'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/constant_with_numbers.latte'), 1];
    }
}
