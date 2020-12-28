<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Tests\Finder\DevFilesFinder;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PackageScoper\Finder\DevFilesFinder;
use Symplify\PackageScoper\HttpKernel\PackageScoperKernel;

final class DevFilesFinderTest extends AbstractKernelTestCase
{
    /**
     * @var DevFilesFinder
     */
    private $devFilesFinder;

    protected function setUp(): void
    {
        $this->bootKernel(PackageScoperKernel::class);
        $this->devFilesFinder = $this->getService(DevFilesFinder::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $source, int $expectedFileCount): void
    {
        $foundFiles = $this->devFilesFinder->findDevFilesPaths($source);
        $this->assertCount($expectedFileCount, $foundFiles);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/dummy_file'], 1];
        yield [[__DIR__ . '/Fixture/paths'], 1];
    }
}
