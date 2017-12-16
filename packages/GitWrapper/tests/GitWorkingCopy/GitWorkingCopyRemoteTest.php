<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\GitWorkingCopy;

use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitWorkingCopy;

final class GitWorkingCopyRemoteTest extends AbstractGitWorkingCopyTestCase
{
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
     * @return mixed[]
     */
    public function addRemoteDataProvider(): array
    {
        return [
            // Test default options: nothing is fetched.
            [
                [],
                [
                    'assertNoRemoteBranches' => [['remote/master', 'remote/remote-branch']],
                    'assertNoGitTag' => ['remote-tag'],
                    'assertNoRemoteMaster' => [],
                ],
            ],
            // The fetch option should retrieve the remote branches and tags,
            // but not set up a master branch.
            [
                ['-f' => true],
                [
                    'assertRemoteBranches' => [['remote/master', 'remote/remote-branch']],
                    'assertGitTag' => ['remote-tag'],
                    'assertNoRemoteMaster' => [],
                ],
            ],
            // The --no-tags options should omit importing tags.
            [
                [
                    '-f' => true,
                    '--no-tags' => true,
                ],
                [
                    'assertRemoteBranches' => [['remote/master', 'remote/remote-branch']],
                    'assertNoGitTag' => ['remote-tag'],
                    'assertNoRemoteMaster' => [],
                ],
            ],
            // The -t option should limit the remote branches that are imported.
            // By default git fetch only imports the tags of the fetched
            // branches. No tags were added to the master branch, so the tag
            // should not be imported.
            [
                [
                    '-f' => true,
                    '-t' => ['master'],
                ],
                [
                    'assertRemoteBranches' => [['remote/master']],
                    'assertNoRemoteBranches' => [['remote/remote-branch']],
                    'assertNoGitTag' => ['remote-tag'],
                    'assertNoRemoteMaster' => [],
                ],
            ],
            // The -t option in combination with the --tags option should fetch
            // all tags, so now the tag should be there.
            [
                [
                    '-f' => true,
                    '-t' => ['master'],
                    '--tags' => true,
                ],
                [
                    'assertRemoteBranches' => [['remote/master']],
                    'assertNoRemoteBranches' => [['remote/remote-branch']],
                    'assertGitTag' => ['remote-tag'],
                    'assertNoRemoteMaster' => [],
                ],
            ],
            // The -m option should set up a remote master branch.
            [
                [
                    '-f' => true,
                    '-m' => 'remote-branch',
                ],
                [
                    'assertRemoteBranches' => [['remote/master', 'remote/remote-branch']],
                    'assertGitTag' => ['remote-tag'],
                    'assertRemoteMaster' => [],
                ],
            ],
        ];
    }

    public function testRemoveRemote(): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();
        $git->addRemote('remote', 'file://' . realpath(self::REMOTE_REPO_DIR));
        $this->assertTrue($git->hasRemote('remote'));

        // The remote should be gone after it is removed.
        $git->removeRemote('remote');
        $this->assertFalse($git->hasRemote('remote'));
    }

    public function testHasRemote(): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();
        // The remote should be absent before it is added.
        $this->assertFalse($git->hasRemote('remote'));
        $git->addRemote('remote', 'file://' . realpath(self::REMOTE_REPO_DIR));
        // The remote should be present after it is added.
        $this->assertTrue($git->hasRemote('remote'));
    }

    public function testGetRemote(): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();
        $path = 'file://' . realpath(self::REMOTE_REPO_DIR);
        $git->addRemote('remote', $path);

        // Both the 'fetch' and 'push' URIs should be populated and point to the
        // correct location.
        $remote = $git->getRemote('remote');
        $this->assertSame($path, $remote['fetch']);
        $this->assertSame($path, $remote['push']);
    }

    public function testGetRemotes(): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();

        // Since our working copy is a clone, the 'origin' remote should be
        // present by default.
        $remotes = $git->getRemotes();
        $this->assertArrayHasKey('origin', $remotes);
        $this->assertArrayNotHasKey('remote', $remotes);

        // If we add a second remote, both it and the 'origin' remotes should be
        // present.
        $git->addRemote('remote', 'file://' . realpath(self::REMOTE_REPO_DIR));
        $remotes = $git->getRemotes();
        $this->assertArrayHasKey('origin', $remotes);
        $this->assertArrayHasKey('remote', $remotes);
    }

    /**
     * @dataProvider getRemoteUrlDataProvider
     */
    public function testGetRemoteUrl(string $remote, string $operation, string $expected): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();
        $git->addRemote('remote', 'file://' . realpath(self::REMOTE_REPO_DIR));
        $this->assertSame('file://' . realpath($expected), $git->getRemoteUrl($remote, $operation));
    }

    /**
     * @return mixed[][]
     */
    public function getRemoteUrlDataProvider(): array
    {
        return [
            ['origin', 'fetch', self::REPO_DIR],
            ['origin', 'push', self::REPO_DIR],
            ['remote', 'fetch', self::REMOTE_REPO_DIR],
            ['remote', 'push', self::REMOTE_REPO_DIR],
        ];
    }

    protected function assertGitTag(GitWorkingCopy $gitWorkingCopy, string $tag): void
    {
        $gitWorkingCopy->run('rev-parse', [$tag]);
    }

    protected function assertNoGitTag(GitWorkingCopy $gitWorkingCopy, string $tag): void
    {
        // Expected result. The tag does not exist.
        $this->expectException(GitException::class);
        $gitWorkingCopy->run('rev-parse', [$tag]);
    }

    protected function assertRemoteMaster(GitWorkingCopy $gitWorkingCopy): void
    {
        $gitWorkingCopy->run('rev-parse', ['remote/HEAD']);
    }

    protected function assertNoRemoteMaster(GitWorkingCopy $gitWorkingCopy): void
    {
        // Expected result. The remote master does not exist.
        $this->expectException(GitException::class);
        $gitWorkingCopy->run('rev-parse', ['remote/HEAD']);
    }

    /**
     * @param mixed[] $branches
     */
    protected function assertRemoteBranches(GitWorkingCopy $gitWorkingCopy, array $branches): void
    {
        foreach ($branches as $branch) {
            $this->assertRemoteBranch($gitWorkingCopy, $branch);
        }
    }

    protected function assertRemoteBranch(GitWorkingCopy $gitWorkingCopy, string $branch): void
    {
        $branches = $gitWorkingCopy->getBranches()->remote();
        $this->assertArrayHasKey($branch, array_flip($branches));
    }

    /**
     * @param string[] $branches
     */
    protected function assertNoRemoteBranches(GitWorkingCopy $gitWorkingCopy, array $branches): void
    {
        foreach ($branches as $branch) {
            $this->assertNoRemoteBranch($gitWorkingCopy, $branch);
        }
    }

    protected function assertNoRemoteBranch(GitWorkingCopy $gitWorkingCopy, string $branch): void
    {
        $branches = $gitWorkingCopy->getBranches()->remote();
        $this->assertArrayNotHasKey($branch, array_flip($branches));
    }

    private function createRemote(): void
    {
        // Create a clone of the working copy that will serve as a remote.
        $git = $this->gitWrapper->cloneRepository('file://' . realpath(self::REPO_DIR), self::REMOTE_REPO_DIR);
        $git->config('user.email', self::CONFIG_EMAIL);
        $git->config('user.name', self::CONFIG_NAME);

        // Make a change to the remote repo.
        file_put_contents(self::REMOTE_REPO_DIR . '/remote.file', "remote code\n");
        $git->add('*');
        $git->commit('Remote change.');

        // Create a branch.
        $branch = 'remote-branch';
        file_put_contents(self::REMOTE_REPO_DIR . '/remote-branch.txt', "${branch}\n");
        $git->checkoutNewBranch($branch);
        $git->add('*');
        $git->commit('Commit remote testing branch.');

        // Create a tag.
        $git->tag('remote-tag');
    }
}
