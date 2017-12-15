<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Event\GitEvent;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\Event\GitSuccessEvent;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\Tests\AbstractGitWrapperTestCase;
use Symplify\GitWrapper\Tests\Event\TestListener;

final class GitListenerTest extends AbstractGitWrapperTestCase
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    public function testListener(): void
    {
        $listener = $this->addListener();
        $this->gitWrapper->version();

        $this->assertTrue($listener->methodCalled('onPrepare'));
        $this->assertTrue($listener->methodCalled('onSuccess'));
        $this->assertFalse($listener->methodCalled('onError'));
    }

    public function testListenerError(): void
    {
        $listener = $this->addListener();
        $this->runBadCommand(true);

        $this->assertTrue($listener->methodCalled('onPrepare'));
        $this->assertFalse($listener->methodCalled('onSuccess'));
        $this->assertTrue($listener->methodCalled('onError'));
    }

    public function testEvent(): void
    {
        $process = new Process('');
        $command = new GitCommand();
        $event = new GitSuccessEvent($this->gitWrapper, $process, $command);

        $this->assertSame($this->gitWrapper, $event->getWrapper());
        $this->assertSame($process, $event->getProcess());
        $this->assertSame($command, $event->getCommand());
    }

    /**
     * Adds the test listener for all events, returns the listener.
     */
    private function addListener(): TestListener
    {
        $listener = new TestListener();

        $this->eventDispatcher->addListener(GitEvents::GIT_PREPARE, [$listener, 'onPrepare']);
        $this->eventDispatcher->addListener(GitEvents::GIT_SUCCESS, [$listener, 'onSuccess']);
        $this->eventDispatcher->addListener(GitEvents::GIT_ERROR, [$listener, 'onError']);

        return $listener;
    }
}
