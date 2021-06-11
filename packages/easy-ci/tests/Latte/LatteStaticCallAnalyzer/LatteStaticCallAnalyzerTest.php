<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteStaticCallAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\Analyzer\StaticCallLatteAnalyzer;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteStaticCallAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var StaticCallLatteAnalyzer
     */
    private $staticCallLatteAnalyzer;

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
            'Method "Project\MailHelper::getUnsubscribeHash()" was not found',
        ];
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/on_variable_static_call.latte'),
            1,
            'Method "$mailHelper::getUnsubscribeHash()" was not found',
        ];
    }
}
