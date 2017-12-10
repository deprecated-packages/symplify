<?php declare(strict_types=1);

namespace GitWrapper\Test;

use GitWrapper\Event\GitLoggerListener;
use GitWrapper\GitCommand;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

final class GitLoggerListenerTest extends AbstractGitWrapperTestCase
{
    public function testGetLogger(): void
    {
        $log = new NullLogger();
        $listener = new GitLoggerListener($log);
        $this->assertEquals($log, $listener->getLogger());
    }

    public function testSetLogLevelMapping(): void
    {
        $listener = new GitLoggerListener(new NullLogger());
        $listener->setLogLevelMapping('test.event', 'test-level');
        $this->assertEquals('test-level', $listener->getLogLevelMapping('test.event'));
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetInvalidLogLevelMapping(): void
    {
        $listener = new GitLoggerListener(new NullLogger());
        $listener->getLogLevelMapping('bad.event');
    }

    public function testRegisterLogger(): void
    {
        $logger = new TestLogger();
        $this->wrapper->addLoggerListener(new GitLoggerListener($logger));
        $git = $this->wrapper->init(self::REPO_DIR, ['bare' => true]);

        $this->assertEquals('Git command preparing to run', $logger->messages[0]);
        $this->assertEquals(
            'Initialized empty Git repository in ' . realpath(self::REPO_DIR) . "/\n",
            $logger->messages[1]
        );
        $this->assertEquals('Git command successfully run', $logger->messages[2]);

        $this->assertArrayHasKey('command', $logger->contexts[0]);
        $this->assertArrayHasKey('command', $logger->contexts[1]);
        $this->assertArrayHasKey('error', $logger->contexts[1]);
        $this->assertArrayHasKey('command', $logger->contexts[2]);

        $this->assertEquals(LogLevel::INFO, $logger->levels[0]);
        $this->assertEquals(LogLevel::DEBUG, $logger->levels[1]);
        $this->assertEquals(LogLevel::INFO, $logger->levels[2]);

        try {
            $logger->clearMessages();
            $git->commit('fatal: This operation must be run in a work tree');
        } catch (\Throwable $e) {
            // Nothing to do, this is expected.
        }

        $this->assertEquals('Error running Git command', $logger->messages[2]);
        $this->assertArrayHasKey('command', $logger->contexts[2]);
        $this->assertEquals(LogLevel::ERROR, $logger->levels[2]);
    }

    public function testLogBypassedCommand(): void
    {
        $logger = new TestLogger();
        $this->wrapper->addLoggerListener(new GitLoggerListener($logger));

        $command = GitCommand::getInstance('status', ['s' => true]);
        $command->bypass();

        $this->wrapper->run($command);

        $this->assertEquals('Git command bypassed', $logger->messages[1]);
        $this->assertArrayHasKey('command', $logger->contexts[1]);
        $this->assertEquals(LogLevel::INFO, $logger->levels[1]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (is_dir(self::REPO_DIR)) {
            $this->filesystem->remove(self::REPO_DIR);
        }
    }
}
