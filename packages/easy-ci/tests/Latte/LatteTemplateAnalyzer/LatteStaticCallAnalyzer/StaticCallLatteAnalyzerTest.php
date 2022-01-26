<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\LatteStaticCallAnalyzer;

use Iterator;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Latte\LatteTemplateAnalyzer\StaticCallLatteAnalyzer;
use Symplify\EasyCI\ValueObject\FileError;
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
    public function test(
        SmartFileInfo $fileInfo,
        int $expectedClassMethodCount,
        string|null $expectedErrorMessage
    ): void {
        $templateErrors = $this->staticCallLatteAnalyzer->analyze([$fileInfo]);
        $this->assertCount($expectedClassMethodCount, $templateErrors);
        // no errors expected
        if ($expectedClassMethodCount === 0) {
            return;
        }
        if ($expectedErrorMessage === null) {
            return;
        }

        $TemplateError = $templateErrors[0];
        $this->assertInstanceOf(FileError::class, $TemplateError);

        $this->assertSame($expectedErrorMessage, $TemplateError->getErrorMessage());
    }

    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/keep_nette_utils.latte'), 0, null];

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
