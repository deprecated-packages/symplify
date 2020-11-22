<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\Analyzer\MissingClassesLatteAnalyzer;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\Analyzer\MissingClassesLatteAnalyzer;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;

final class MissingClassesLatteAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var MissingClassesLatteAnalyzer
     */
    private $missingClassesLatteAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->missingClassesLatteAnalyzer = self::$container->get(MissingClassesLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $result = $this->missingClassesLatteAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $result);
    }

    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_classes.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_var_type.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/existing_class_instanceof.latte'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/non_classes.latte'), 0];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_classes.latte'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_class_instanceof.latte'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_var_type.latte'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/missing_input.latte'), 2];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_scalar_vartype.latte'), 0];
    }
}
