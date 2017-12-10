<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Event\GitEvent;
use Symplify\GitWrapper\GitCommand;

final class GitListenerTest extends AbstractGitWrapperTestCase
{
    public function testListener(): void
    {
        $listener = $this->addListener();
        $this->wrapper->version();

        $this->assertTrue($listener->methodCalled('onPrepare'));
        $this->assertTrue($listener->methodCalled('onSuccess'));
        $this->assertFalse($listener->methodCalled('onError'));
        $this->assertFalse($listener->methodCalled('onBypass'));
    }

    public function testListenerError(): void
    {
        $listener = $this->addListener();
        $this->runBadCommand(true);

        $this->assertTrue($listener->methodCalled('onPrepare'));
        $this->assertFalse($listener->methodCalled('onSuccess'));
        $this->assertTrue($listener->methodCalled('onError'));
        $this->assertFalse($listener->methodCalled('onBypass'));
    }

    public function testGitBypass(): void
    {
        $this->addBypassListener();
        $listener = $this->addListener();

        $output = $this->wrapper->version();

        $this->assertTrue($listener->methodCalled('onPrepare'));
        $this->assertFalse($listener->methodCalled('onSuccess'));
        $this->assertFalse($listener->methodCalled('onError'));
        $this->assertTrue($listener->methodCalled('onBypass'));

        $this->assertEmpty($output);
    }

    public function testEvent(): void
    {
        $process = new Process('');
        $command = GitCommand::getInstance();
        $event = new GitEvent($this->wrapper, $process, $command);

        $this->assertSame($this->wrapper, $event->getWrapper());
        $this->assertSame($process, $event->getProcess());
        $this->assertSame($command, $event->getCommand());
    }
}
