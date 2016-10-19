<?php

declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ModularDoctrineFilters\Tests\Controller\SomeController;

final class CompleteTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Kernel
     */
    private $kernel;

    protected function setUp()
    {
        $this->kernel = new AppKernel('test_env', true);
        $this->kernel->boot();

        $this->entityManager = $this->kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');
    }

    public function testEnableFiltersViaSubscriber()
    {
        $request = new Request();
        $request->attributes->set('_controller', SomeController::class.'::someAction');

        $filters = $this->entityManager->getFilters();
        $this->assertCount(0, $filters->getEnabledFilters());

        $this->kernel->handle($request);
        $this->assertCount(1, $filters->getEnabledFilters());
    }
}
