<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\Analyzer\LatteStaticCallAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\Analyzer\StaticCallLatteAnalyzer;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
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
        $latteErrors = $this->staticCallLatteAnalyzer->analyze([$fileInfo]);
        $this->assertCount($expectedClassMethodCount, $latteErrors);

        $latteError = $latteErrors[0];
        $this->assertInstanceOf(LatteError::class, $latteError);

        $this->assertSame($expectedClassMethodName, $latteError->getErrorMessage());
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
