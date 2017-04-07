<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\DefaultAutowire\Tests\Resources\Repository\SomeRepository;
use Symplify\DefaultAutowire\Tests\Source\FakeEntityManager;
use Symplify\DefaultAutowire\Tests\Source\SomeAutowiredService;
use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class CompleteTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function setUp(): void
    {
        $kernel = new AppKernel;
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    public function testSomeServiceAutowire(): void
    {
        /** @var SomeAutowiredService $someAutowiredService */
        $someAutowiredService = $this->container->get('some_autowired_service');

        $this->assertInstanceOf(SomeAutowiredService::class, $someAutowiredService);
        $this->assertInstanceOf(SomeService::class, $someAutowiredService->getSomeService());

        $this->assertInstanceOf(EventDispatcherInterface::class, $someAutowiredService->getEventDispatcher());
        $this->assertInstanceOf(EventDispatcher::class, $someAutowiredService->getEventDispatcher());
        $this->assertNotInstanceOf(TraceableEventDispatcher::class, $someAutowiredService->getEventDispatcher());
    }

    public function testRepositoryAutowire(): void
    {
        /** @var SomeRepository $someRepository */
        $someRepository = $this->container->get('some_repository');
        $this->assertInstanceOf(SomeRepository::class, $someRepository);
        $this->assertInstanceOf(FakeEntityManager::class, $someRepository->getEntityManager());
    }
}
