<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\Analyzer\MissingClassConstantLatteAnalyzer;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\Analyzer\MissingClassConstantLatteAnalyzer;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;

final class MissingClassConstantLatteAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var MissingClassConstantLatteAnalyzer
     */
    private $missingClassConstantLatteAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->missingClassConstantLatteAnalyzer = self::$container->get(MissingClassConstantLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $fileInfosWithMissingClassConstantErrors = $this->missingClassConstantLatteAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $fileInfosWithMissingClassConstantErrors);
    }

    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_constant.twig'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_constant.twig'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/inside_foreach.twig'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_var_type.latte'), 0];
    }
}
