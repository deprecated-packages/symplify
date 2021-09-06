<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Neon\Application\NeonFilesProcessor;

use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Neon\Application\NeonFilesProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonFilesProcessorTest extends AbstractKernelTestCase
{
    private NeonFilesProcessor $neonFilesProcessor;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->neonFilesProcessor = $this->getService(NeonFilesProcessor::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo, int $expectedErrorFileCount): void
    {
        $fileErrors = $this->neonFilesProcessor->processFileInfos([$fileInfo]);
        $this->assertCount($expectedErrorFileCount, $fileErrors);
    }

    public function provideData(): \Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/complex_neon.neon'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/simple_neon.neon'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/complex_non_service.neon'), 0];
    }
}
