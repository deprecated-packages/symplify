<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Neon\Application\NeonFilesProcessor;

use Iterator;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Neon\Application\NeonFilesProcessor;
use Symplify\EasyCI\ValueObject\FileError;
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
     * @param FileErrorInterface[] $expectedFileErrors
     */
    public function test(SmartFileInfo $fileInfo, array $expectedFileErrors): void
    {
        $fileErrors = $this->neonFilesProcessor->processFileInfos([$fileInfo]);

        $expectedErrorCount = count($expectedFileErrors);
        $this->assertCount($expectedErrorCount, $fileErrors);

        $this->assertEquals($expectedFileErrors, $fileErrors);
    }

    /**
     * @return Iterator<SmartFileInfo[]|array<FileErrorInterface[]>>
     */
    public function provideData(): Iterator
    {
        $complexNeonFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/complex_neon.neon');
        $fileError = new FileError(
            'Complex entity found "Service(@param)".' . PHP_EOL . 'Change it to explicit syntax with named keys, that is easier to read.',
            $complexNeonFileInfo
        );
        yield [$complexNeonFileInfo, [$fileError]];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/simple_neon.neon'), []];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/service_with_setup.neon'), []];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/complex_non_service.neon'), []];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_argument_name.neon'), []];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_not.neon'), []];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/skip_empty_service.neon'), []];
    }
}
