<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Nette\Utils\Random;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitWrapper;
use Symplify\GitWrapper\Tests\Event\TestBypassEventSubscriber;
use Symplify\GitWrapper\Tests\EventSubscriber\Source\TestEventSubscriber;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractGitWrapperTestCase extends TestCase
{
    /**
     * @var string
     */
    protected const REPO_DIR = __DIR__ . '/build/tests/repo';

    /**
     * @var string
     */
    protected const WORKING_DIR = __DIR__ . '/build/tests/wc';

    /**
     * @var string
     */
    protected const CONFIG_EMAIL = 'testing@email.com';

    /**
     * @var string
     */
    protected const CONFIG_NAME = 'Testing name';

    /**
     * @see https://regex101.com/r/ordjZ6/1
     * @var string
     */
    private const GIT_VERSION_REGEX = '#^git version [.0-9]+#';

    protected SmartFileSystem $smartFileSystem;

    protected GitWrapper $gitWrapper;

    protected function setUp(): void
    {
        $this->smartFileSystem = new SmartFileSystem();
        $this->gitWrapper = new GitWrapper('git');
    }

    protected function registerAndReturnEventSubscriber(): TestEventSubscriber
    {
        $eventDispatcher = $this->gitWrapper->getDispatcher();
        $testEventSubscriber = new TestEventSubscriber();
        $eventDispatcher->addSubscriber($testEventSubscriber);

        return $testEventSubscriber;
    }

    /**
     * Adds the bypass event subscriber so that Git commands are not run.
     */
    protected function createRegisterAndReturnBypassEventSubscriber(): TestBypassEventSubscriber
    {
        $eventDispatcher = $this->gitWrapper->getDispatcher();
        $testBypassEventSubscriber = new TestBypassEventSubscriber();
        $eventDispatcher->addSubscriber($testBypassEventSubscriber);

        return $testBypassEventSubscriber;
    }

    /**
     * Asserts a correct Git version string was returned.
     *
     * @param string $version The version returned by the `git --version` command.
     */
    protected function assertGitVersion(string $version): void
    {
        $match = Strings::match($version, self::GIT_VERSION_REGEX);
        $this->assertNotEmpty($match);
    }

    protected function runBadCommand(bool $catchException = false): void
    {
        try {
            $this->gitWrapper->git('a-bad-command');
        } catch (GitException $gitException) {
            if ($catchException) {
                return;
            }

            throw $gitException;
        }
    }

    protected function randomString(): string
    {
        return Random::generate();
    }
}
