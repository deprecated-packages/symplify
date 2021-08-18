<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\LatteStaticCallAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\LatteTemplateAnalyzer\StaticCallLatteAnalyzer;
use Symplify\EasyCI\ValueObject\TemplateError;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticCallLatteAnalyzerTest extends AbstractKernelTestCase
{
    private StaticCallLatteAnalyzer $staticCallLatteAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->staticCallLatteAnalyzer = $this->getService(StaticCallLatteAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo, int $expectedClassMethodCount, string $expectedClassMethodName): void
    {
        $templateErrors = $this->staticCallLatteAnalyzer->analyze([$fileInfo]);
        $this->assertCount($expectedClassMethodCount, $templateErrors);

        $TemplateError = $templateErrors[0];
        $this->assertInstanceOf(TemplateError::class, $TemplateError);

        $this->assertSame($expectedClassMethodName, $TemplateError->getErrorMessage());
    }

    public function provideData(): Iterator
    {
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/simple_static_call.latte'),
            1,
            'Static call "Project\MailHelper::getUnsubscribeHash()" should not be used in template, move to filter provider instead',
        ];
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/on_variable_static_call.latte'),
            1,
            'Static call "$mailHelper::getUnsubscribeHash()" should not be used in template, move to filter provider instead',
        ];
    }
}
