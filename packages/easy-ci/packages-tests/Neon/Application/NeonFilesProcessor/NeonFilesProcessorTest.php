<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Neon\Application\NeonFilesProcessor;

use Iterator;
use Symplify\EasyCI\Kernel\EasyCIKernel;
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

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/complex_neon.neon'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/simple_neon.neon'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/service_with_setup.neon'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/complex_non_service.neon'), 0];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_not.neon'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_empty_service.neon'), 0];
    }
}
