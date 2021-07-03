<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\StaticDetector\StaticScanner;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use Symplify\EasyCI\StaticDetector\StaticScanner;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticReport;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticScannerTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
    }

    public function testStaticClassMethodDetection(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/StaticCallFile.php.inc');
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $this->assertSame(1, $staticReport->getStaticClassMethodCount());

        $staticClassMethodWithStaticCalls = $staticReport->getStaticClassMethodsWithStaticCalls()[0];
        $this->assertCount(0, $staticClassMethodWithStaticCalls->getStaticCalls());
    }

    public function testFileLocationWithLine(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/StaticCallFile.php.inc');
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $staticClassMethodWithStaticCalls = $staticReport->getStaticClassMethodsWithStaticCalls()[0];

        $this->assertStringEndsWith(
            'StaticScanner/Fixture/StaticCallFile.php.inc:9',
            $staticClassMethodWithStaticCalls->getStaticCallFileLocationWithLine()
        );
    }

    /**
     * @dataProvider provideData()
     */
    public function testClassMethodAndStaticCallCount(
        string $filePath,
        int $expectedClassMethodCount,
        int $expectedStaticCallCount
    ): void {
        $fileInfo = new SmartFileInfo($filePath);
        $staticReport = $this->createStaticReportFromFileInfo($fileInfo);

        $this->assertSame($expectedClassMethodCount, $staticReport->getStaticClassMethodCount());
        $this->assertSame($expectedStaticCallCount, $staticReport->getStaticCallsCount());
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/StaticSelfFile.php.inc', 1, 1];
        yield [__DIR__ . '/Fixture/StaticParentFile.php.inc', 1, 1];
        yield [__DIR__ . '/Fixture/SomeEventSubscriber.php.inc', 0, 0];
    }

    private function createStaticReportFromFileInfo(SmartFileInfo $fileInfo): StaticReport
    {
        $staticScanner = $this->getService(StaticScanner::class);
        $staticScanner->scanFileInfos([$fileInfo]);

        $staticNodeCollector = $this->getService(StaticNodeCollector::class);
        return $staticNodeCollector->generateStaticReport();
    }
}
