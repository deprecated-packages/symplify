<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\LatteStaticCallAnalyzer;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\TemplateChecker\LatteStaticCallAnalyzer;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;

final class LatteStaticCallAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var LatteStaticCallAnalyzer
     */
    private $latteStaticCallAnalyzer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->latteStaticCallAnalyzer = self::$container->get(LatteStaticCallAnalyzer::class);
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
