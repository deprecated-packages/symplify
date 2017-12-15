<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Event\GitEvent;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\Event\GitSuccessEvent;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\Tests\AbstractGitWrapperTestCase;
use Symplify\GitWrapper\Tests\EventSubscriber\TestSubscriber;

final class GitSubscriberTest extends AbstractGitWrapperTestCase
{
    /**
     * @var TestSubscriber
     */
    private $testSubscriber;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testSubscriber = new TestSubscriber();

        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
        $this->eventDispatcher->addSubscriber($this->testSubscriber);
    }

    public function testSubscriber(): void
    {
        $this->gitWrapper->version();

        $this->assertTrue($this->testSubscriber->wasMethodCalled('onPrepare'));
        $this->assertTrue($this->testSubscriber->wasMethodCalled('onSuccess'));
        $this->assertFalse($this->testSubscriber->wasMethodCalled('onError'));
    }

    public function testListenerError(): void
    {
//        $listener = $this->addListener();
        $this->runBadCommand(true);

        $this->assertTrue($listener->wasMethodCalled('onPrepare'));
        $this->assertFalse($listener->wasMethodCalled('onSuccess'));
        $this->assertTrue($listener->wasMethodCalled('onError'));
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
}
