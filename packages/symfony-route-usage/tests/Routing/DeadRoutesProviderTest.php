<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Tests\Routing;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SymfonyRouteUsage\Routing\DeadRoutesProvider;
use Symplify\SymfonyRouteUsage\Tests\Helper\DatabaseLoaderHelper;
use Symplify\SymfonyRouteUsage\Tests\HttpKernel\SymfonyRouteUsageKernel;

final class DeadRoutesProviderTest extends AbstractKernelTestCase
{
    /**
     * @var DeadRoutesProvider
     */
    private $deadRoutesProvider;

    protected function setUp(): void
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $this->markTestSkipped('Out of order, needs to fix database loading in GitHub Actions');
=======
        $this->markTestSkipped('Temporary broken');
>>>>>>> 7e1cbd8ad... fixup! fixup! misc

=======
>>>>>>> 018230f3b... remove migrify kernel, update composer.json
        $this->bootKernel(SymfonyRouteUsageKernel::class);

        $databaseLoaderHelper = new DatabaseLoaderHelper(self::$container);
        $databaseLoaderHelper->disableDoctrineLogger();
        $databaseLoaderHelper->createDatabase();

        $this->deadRoutesProvider = self::$container->get(DeadRoutesProvider::class);
    }

    public function test(): void
    {
        $deadRoutes = $this->deadRoutesProvider->provide();
        $this->assertCount(1, $deadRoutes);

        $this->assertArrayHasKey('acme_privacy', $deadRoutes);
    }
}
