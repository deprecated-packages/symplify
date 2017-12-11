<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Nette\Utils\Random;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\GitException;
use Symplify\GitWrapper\GitWrapper;
use Symplify\GitWrapper\Tests\Event\TestBypassListener;

abstract class AbstractGitWrapperTestCase extends TestCase
{
    /**
     * @var string
     */
    protected const REPO_DIR = __DIR__ .'/temp/repository';

    /**
     * @var string
     */
    protected const WORKING_DIR = __DIR__ . '/temp/working-dir';

    /**
     * @var string
     */
    protected const CONFIG_EMAIL = 'opensource@chrispliakas.com';

    /**
     * @var string
     */
    protected const CONFIG_NAME = 'Chris Pliakas';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var GitWrapper
     */
    protected $gitWrapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->gitWrapper = new GitWrapper();
    }

    protected function randomString(int $length = 8): string
    {
        return Random::generate($length);
    }

    /**
     * Adds the bypass listener so that Git commands are not run.
     */
    public function addBypassListener(): TestBypassListener
    {
        $listener = new TestBypassListener();
        $dispatcher = $this->gitWrapper->getDispatcher();
        $dispatcher->addListener(GitEvents::GIT_PREPARE, [$listener, 'onPrepare'], -5);
        return $listener;
    }

    /**
     * Asserts a correct Git version string was returned.
     *
     * The version returned by the `git --version` command.
     */
    public function assertGitVersion(string $type): void
    {
        $match = preg_match('/^git version [.0-9]+/', $type);
        $this->assertNotEmpty($match);
    }

    /**
     * Executes a bad command.
     *
     * @param bool $catchException Whether to catch the exception to continue script execution.
     */
    public function runBadCommand(bool $catchException = false): void
    {
        try {
            $this->gitWrapper->git('a-bad-command');
        } catch (GitException $gitException) {
            if (! $catchException) {
                throw $gitException;
            }
        }
    }
}
