<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LatteStaticCallAnalyzer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Latte\LatteStaticCallAnalyzer;
use Symplify\EasyCI\ValueObject\ClassMethodName;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteStaticCallAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var LatteStaticCallAnalyzer
     */
    private $latteStaticCallAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->latteStaticCallAnalyzer = $this->getService(LatteStaticCallAnalyzer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo, int $expectedClassMethodCount, string $expectedClassMethodName): void
    {
        $classMethodNames = $this->latteStaticCallAnalyzer->analyzeFileInfos([$fileInfo]);

        $this->assertCount($expectedClassMethodCount, $classMethodNames);

        $classMethodName = $classMethodNames[0];
        $this->assertInstanceOf(ClassMethodName::class, $classMethodName);

        $this->assertSame($expectedClassMethodName, $classMethodName->getClassMethodName());
    }

    public function provideData(): Iterator
    {
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/simple_static_call.latte'),
            1,
            'Project\MailHelper::getUnsubscribeHash',
        ];
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/on_variable_static_call.latte'),
            1,
            '$mailHelper::getUnsubscribeHash',
        ];
    }
}
