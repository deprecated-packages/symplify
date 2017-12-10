<?php declare(strict_types=1);

namespace GitWrapper\Test;

use GitWrapper\Event\GitEvents;
use GitWrapper\GitException;
use GitWrapper\GitWrapper;
use GitWrapper\Test\Event\TestBypassListener;
use GitWrapper\Test\Event\TestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGitWrapperTestCase extends TestCase
{
    public const REPO_DIR = 'build/test/repo';
    public const WORKING_DIR = 'build/test/wc';
    public const CONFIG_EMAIL = 'opensource@chrispliakas.com';
    public const CONFIG_NAME = 'Chris Pliakas';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \GitWrapper\GitWrapper
     */
    protected $wrapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->wrapper = new GitWrapper();
    }

    /**
     * Generates a random string.
     *
     * @see http://api.drupal.org/api/drupal/modules%21simpletest%21drupal_web_test_case.php/function/DrupalTestCase%3A%3ArandomName/7
     */
    public function randomString(int $length = 8): string
    {
        $values = array_merge(range(65, 90), range(97, 122), range(48, 57));
        $max = count($values) - 1;
        $str = chr(random_int(97, 122));
        for ($i = 1; $i < $length; $i++) {
            $str .= chr($values[random_int(0, $max)]);
        }

        return $str;
    }

    /**
     * Adds the test listener for all events, returns the listener.
     */
    public function addListener(): \GitWrapper\Test\Event\TestListener
    {
        $dispatcher = $this->wrapper->getDispatcher();
        $listener = new TestListener();

        $dispatcher->addListener(GitEvents::GIT_PREPARE, [$listener, 'onPrepare']);
        $dispatcher->addListener(GitEvents::GIT_SUCCESS, [$listener, 'onSuccess']);
        $dispatcher->addListener(GitEvents::GIT_ERROR, [$listener, 'onError']);
        $dispatcher->addListener(GitEvents::GIT_BYPASS, [$listener, 'onBypass']);

        return $listener;
    }

    /**
     * Adds the bypass listener so that Git commands are not run.
     */
    public function addBypassListener(): \GitWrapper\Test\Event\TestBypassListener
    {
        $listener = new TestBypassListener();
        $dispatcher = $this->wrapper->getDispatcher();
        $dispatcher->addListener(GitEvents::GIT_PREPARE, [$listener, 'onPrepare'], -5);
        return $listener;
    }

    /**
     * Asserts a correct Git version string was returned.
     *
     * @param type $version
     *   The version returned by the `git --version` command.
     */
    public function assertGitVersion(type $version): void
    {
        $match = preg_match('/^git version [.0-9]+/', $version);
        $this->assertNotEmpty($match);
    }

    /**
     * Executes a bad command.
     *
     * @param bool $catchException Whether to catch the exception to continue script execution, defaults to false.
     */
    public function runBadCommand(bool $catchException = false): void
    {
        try {
            $this->wrapper->git('a-bad-command');
        } catch (GitException $e) {
            if (!$catchException) {
                throw $e;
            }
        }
    }
}
