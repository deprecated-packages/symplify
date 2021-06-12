<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Tests\AssertContainsFileLineExtractor;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PHPUnitUpgrader\AssertContainsFileLineExtractor;
use Symplify\PHPUnitUpgrader\HttpKernel\PHPUnitUpgraderKernel;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsFileLineExtractorTest extends AbstractKernelTestCase
{
    private AssertContainsFileLineExtractor $assertContainsFileLineExtractor;

    protected function setUp(): void
    {
        $this->bootKernel(PHPUnitUpgraderKernel::class);
        $this->assertContainsFileLineExtractor = $this->getService(AssertContainsFileLineExtractor::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/phpunit_error_report.txt');

        $fileLines = $this->assertContainsFileLineExtractor->extract($fileInfo);
        $this->assertCount(1, $fileLines);

        $fileLine = $fileLines[0];
        $this->assertSame(99, $fileLine->getLine());
        $this->assertSame('somePath.php', $fileLine->getFilePath());
    }
}
