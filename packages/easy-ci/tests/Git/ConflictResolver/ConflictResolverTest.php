<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Git\ConflictResolver;

use Iterator;
use Symplify\EasyCI\Git\ConflictResolver;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConflictResolverTest extends AbstractKernelTestCase
{
    private ConflictResolver $conflictResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->conflictResolver = $this->getService(ConflictResolver::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo, int $expectedConflictCount): void
    {
        $unresolvedConflictCount = $this->conflictResolver->extractFromFileInfo($fileInfo);
        $this->assertSame($expectedConflictCount, $unresolvedConflictCount);
    }

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/some_file.txt'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/correct_file.txt'), 0];
    }
}
