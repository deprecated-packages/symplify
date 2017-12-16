<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\GitWorkingCopy;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\GitWrapper\GitWorkingCopy;
use Symplify\GitWrapper\Tests\AbstractGitWrapperTestCase;

abstract class AbstractGitWorkingCopyTestCase extends AbstractGitWrapperTestCase
{
    /**
     * @var string
     */
    protected const REMOTE_REPO_DIR = __DIR__ . '/temp/remote';

    /**
     * @var string
     */
    protected const CONFIG_EMAIL = 'opensource@chrispliakas.com';

    /**
     * @var string
     */
    protected const CONFIG_NAME = 'Chris Pliakas';

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);

        // Create the local repository.
        $this->gitWrapper->init(self::REPO_DIR, ['bare' => true]);

        // Clone the local repository.
        $directory = __DIR__ . '/temp/wc_init';

        $git = $this->gitWrapper->cloneRepository('file://' . realpath(self::REPO_DIR), $directory);
        $git->config('user.email', self::CONFIG_EMAIL);
        $git->config('user.name', self::CONFIG_NAME);

        // Create the initial structure.
        file_put_contents($directory . '/change.me', "unchanged\n");
        $this->filesystem->touch($directory . '/move.me');
        $this->filesystem->mkdir($directory . '/a.directory', 0755);
        $this->filesystem->touch($directory . '/a.directory/remove.me');

        // Initial commit
        $git->add('*');
        $git->commit('Initial commit.');
        $git->push('origin', 'master', ['u' => true]);

        // Create a branch, add a file
        $branch = 'test-branch';
        file_put_contents($directory . '/branch.txt', "${branch}\n");
        $git->checkoutNewBranch($branch);

        $git->add('branch.txt');
        $git->commit('Committed testing branch.');
        $git->push('origin', $branch, ['u' => true]);

        // Create a tag of the branch
        $git->tag('test-tag');
        $git->pushTags();

        $this->filesystem->remove($directory);
    }

    /**
     * Removes the local repository.
     */
    protected function tearDown(): void
    {
        $this->filesystem->remove(self::REPO_DIR);
        $this->filesystem->remove(__DIR__ . '/temp/wc_init');

        if (is_dir(self::WORKING_DIR)) {
            $this->filesystem->remove(self::WORKING_DIR);
        }

        if (is_dir(self::REMOTE_REPO_DIR)) {
            $this->filesystem->remove(self::REMOTE_REPO_DIR);
        }
    }

    /**
     * @dataProvider addRemoteDataProvider
     * @param mixed[] $options
     * @param mixed[] $asserts
     */
    public function testAddRemote(array $options, array $asserts): void
    {
        $this->createRemote();

        $git = $this->getWorkingCopy();
        $git->addRemote('remote', 'file://' . realpath(self::REMOTE_REPO_DIR), $options);

        $this->assertTrue($git->hasRemote('remote'));

        foreach ($asserts as $method => $parameters) {
            array_unshift($parameters, $git);
            $this->{$method}(...$parameters);
        }
    }

    /**
     * Clones the local repo and returns an initialized GitWorkingCopy object.
     */
    protected function getWorkingCopy(string $directory = self::WORKING_DIR): GitWorkingCopy
    {
        $git = $this->gitWrapper->workingCopy($directory);
        $git->cloneRepository('file://' . realpath(self::REPO_DIR));
        $git->config('user.email', self::CONFIG_EMAIL);
        $git->config('user.name', self::CONFIG_NAME);
        $git->clearOutput();

        return $git;
    }
}
