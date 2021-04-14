<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber;

use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Symplify\GitWrapper\EventSubscriber\GitLoggerEventSubscriber;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\Tests\AbstractGitWrapperTestCase;
use Symplify\GitWrapper\Tests\EventSubscriber\Source\TestLogger;

final class GitLoggerEventSubscriberTest extends AbstractGitWrapperTestCase
{
    protected function tearDown(): void
    {
        if (is_dir(self::REPO_DIR)) {
            $this->smartFileSystem->remove(self::REPO_DIR);
        }
    }

    public function testSetLogLevelMapping(): void
    {
        $gitLoggerEventSubscriber = new GitLoggerEventSubscriber(new NullLogger());
        $gitLoggerEventSubscriber->setLogLevelMapping('test.event', 'test-level');

        $logLevelMapping = $gitLoggerEventSubscriber->getLogLevelMapping('test.event');
        $this->assertSame('test-level', $logLevelMapping);
    }

    public function testGetInvalidLogLevelMapping(): void
    {
        $this->expectException(GitException::class);

        $gitLoggerEventSubscriber = new GitLoggerEventSubscriber(new NullLogger());
        $gitLoggerEventSubscriber->getLogLevelMapping('bad.event');
    }

    public function testRegisterLogger(): void
    {
        $testLogger = new TestLogger();
        $this->gitWrapper->addLoggerEventSubscriber(new GitLoggerEventSubscriber($testLogger));

        $this->gitWrapper->init(self::REPO_DIR, [
            'bare' => true,
        ]);

        $this->assertContains('Git command preparing to run', $testLogger->messages);
        $this->assertContains(
            'Initialized empty Git repository in ' . realpath(self::REPO_DIR) . "/\n",
            $testLogger->messages
        );

        $this->assertContains('Git command successfully run', $testLogger->messages);
    }

    public function testLogBypassedCommand(): void
    {
        $testLogger = new TestLogger();
        $this->gitWrapper->addLoggerEventSubscriber(new GitLoggerEventSubscriber($testLogger));

        $gitCommand = new GitCommand('status', [
            's' => true,
        ]);
        $gitCommand->bypass();

        $this->gitWrapper->run($gitCommand);

        $this->assertSame('Git command bypassed', $testLogger->messages[1]);
        $this->assertArrayHasKey('command', $testLogger->contexts[1]);
        $this->assertSame(LogLevel::INFO, $testLogger->levels[1]);
    }
}
