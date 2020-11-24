<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Tests\EntityRepository;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SymfonyRouteUsage\Entity\RouteVisit;
use Symplify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;
use Symplify\SymfonyRouteUsage\Tests\Helper\DatabaseLoaderHelper;
use Symplify\SymfonyRouteUsage\Tests\HttpKernel\SymfonyRouteUsageKernel;

final class RouteVisitRepositoryTest extends AbstractKernelTestCase
{
    /**
     * @var RouteVisitRepository
     */
    private $routeVisitRepository;

    protected function setUp(): void
    {
        $this->markTestSkipped('Temporary broken');

        $this->bootKernel(SymfonyRouteUsageKernel::class);

        $databaseLoaderHelper = new DatabaseLoaderHelper(self::$container);
        $databaseLoaderHelper->disableDoctrineLogger();
        $databaseLoaderHelper->createDatabase();

        $this->routeVisitRepository = self::$container->get(RouteVisitRepository::class);
    }

    public function test(): void
    {
        $routeVisit = new RouteVisit('some_route', "{'route':'params'}", 'SomeController', 'some_hash');

        $this->routeVisitRepository->save($routeVisit);

        $routeVisits = $this->routeVisitRepository->fetchAll();
        $this->assertCount(1, $routeVisits);

        $routeVisit = $routeVisits[0];
        $this->assertSame(1, $routeVisit->getVisitCount());
    }
}
