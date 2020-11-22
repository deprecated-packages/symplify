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
    /**
     * @var ConflictResolver
     */
    private $conflictResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->conflictResolver = self::$container->get(ConflictResolver::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedConflictCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $unresolvedConflictCount = $this->conflictResolver->extractFromFileInfo($fileInfo);
        $this->assertSame($expectedConflictCount, $unresolvedConflictCount);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/some_file.txt', 1];
        yield [__DIR__ . '/Fixture/correct_file.txt', 0];
    }
}
