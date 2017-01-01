<?php

declare(strict_types=1);

namespace Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony;

use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony\Source\DummyService;
use Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony\Source\SomeCommand;

final class CompleteTest extends TestCase
{
    /**
     * @var Application
     */
    private $consoleApplication;

    protected function setUp()
    {
        $this->consoleApplication = new Application(new AppKernel());
    }

    public function testTags()
    {
        /** @var SomeCommand $someCommand */
        $someCommand = $this->consoleApplication->get('some_command');
        $this->assertInstanceOf(SomeCommand::class, $someCommand);
    }

    public function testAutowire()
    {
        /** @var SomeCommand $someCommand */
        $someCommand = $this->consoleApplication->get('some_command');
        $this->assertInstanceOf(Finder::class, $someCommand->getFinder());
    }

    public function testMethodCalls()
    {
        /** @var SomeCommand $someCommand */
        $someCommand = $this->consoleApplication->get('some_command');
        $this->assertInstanceOf(DummyService::class, $someCommand->getDummyService());
    }

    public function testTagsForEventDispatcher()
    {
        $kernel = new AppKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');
        $this->assertInstanceOf(EventDispatcher::class, $eventDispatcher);

        $subscribers = $eventDispatcher->getListeners('some_event');
        $this->assertCount(1, $subscribers);
    }
}
