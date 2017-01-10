<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\Adapter\Symfony;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\AutoServiceRegistration\Naming\ServiceNaming;
use Symplify\AutoServiceRegistration\Tests\Adapter\Symfony\CompleteTestSource\AnotherController;

final class CompleteTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $kernel = new AppKernel;
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    public function testGetController()
    {
        $serviceId = ServiceNaming::createServiceIdFromClass(AnotherController::class);
        // dump($serviceId);

        $this->assertInstanceOf(AnotherController::class, $this->container->get($serviceId));
    }
}
