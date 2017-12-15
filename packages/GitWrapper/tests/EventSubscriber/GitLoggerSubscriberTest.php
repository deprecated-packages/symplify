<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber;

use DomainException;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\GitWrapper\EventSubscriber\GitLoggerEventSubscriber;
use Symplify\GitWrapper\Tests\AbstractGitWrapperTestCase;
use Symplify\GitWrapper\Tests\Logging\TestLogger;
use Throwable;

final class GitLoggerSubscriberTest extends AbstractGitWrapperTestCase
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_dir(self::REPO_DIR)) {
            $this->filesystem->remove(self::REPO_DIR);
        }

        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    public function testRegisterLogger(): void
    {
        // @todo create container with config

        $logger = new TestLogger();
        $this->eventDispatcher->addSubscriber(new GitLoggerEventSubscriber($logger));
        $git = $this->gitWrapper->init(self::REPO_DIR, ['bare' => true]);

        $this->assertSame('Git command preparing to run', $logger->messages[0]);
        $this->assertSame(
            'Initialized empty Git repository in ' . realpath(self::REPO_DIR) . "/\n",
            $logger->messages[1]
        );
        $this->assertSame('Git command successfully run', $logger->messages[2]);

        $this->assertArrayHasKey('command', $logger->contexts[0]);
        $this->assertArrayHasKey('command', $logger->contexts[1]);
        $this->assertArrayHasKey('error', $logger->contexts[1]);
        $this->assertArrayHasKey('command', $logger->contexts[2]);

        $this->assertSame(LogLevel::INFO, $logger->levels[0]);
        $this->assertSame(LogLevel::DEBUG, $logger->levels[1]);
        $this->assertSame(LogLevel::INFO, $logger->levels[2]);

        try {
            $logger->clearMessages();
            $git->commit('fatal: This operation must be run in a work tree');
        } catch (Throwable $exception) {
            // Nothing to do, this is expected.
        }

        $this->assertSame('Error running Git command', $logger->messages[2]);
        $this->assertArrayHasKey('command', $logger->contexts[2]);
        $this->assertSame(LogLevel::ERROR, $logger->levels[2]);
    }
}
