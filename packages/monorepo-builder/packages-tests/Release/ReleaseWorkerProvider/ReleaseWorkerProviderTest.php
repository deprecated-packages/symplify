<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Release\ReleaseWorkerProvider;

use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Release\ReleaseWorkerProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ReleaseWorkerProviderTest extends AbstractKernelTestCase
{
    private ReleaseWorkerProvider $releaseWorkerProvider;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/config/all_release_workers.php']);
        $this->releaseWorkerProvider = $this->getService(ReleaseWorkerProvider::class);
    }

    public function test(): void
    {
        $releaseWorkers = $this->releaseWorkerProvider->provide();
        $this->assertCount(7, $releaseWorkers);
    }
}
