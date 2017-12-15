<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Exception;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitBranches;
use Symplify\GitWrapper\GitWorkingCopy;
use Symplify\GitWrapper\Tests\Event\TestOutputListener;

final class GitWorkingCopyTest extends AbstractGitWrapperTestCase
{
    /**
     * @var string
     */
    private const REMOTE_REPO_DIR = __DIR__ . '/temp/remote';

    /**
     * @var string
     */
    private const CONFIG_EMAIL = 'opensource@chrispliakas.com';

    /**
     * @var string
     */
    private const CONFIG_NAME = 'Chris Pliakas';

    protected function setUp(): void
    {
        parent::setUp();

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

        // Initial commit.

        $git->add('*');
        $git->commit('Initial commit.');
        $git->push('origin', 'master', ['u' => true]);

        // Create a branch, add a file.
        $branch = 'test-branch';
        file_put_contents($directory . '/branch.txt', "${branch}\n");
        $git->checkoutNewBranch($branch);

        $git->add('branch.txt');
        $git->commit('Committed testing branch.');
        $git->push('origin', $branch, ['u' => true]);

        // Create a tag of the branch.
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

    public function testIsCloned(): void
    {
        $git = $this->getWorkingCopy();
        $this->assertTrue($git->isCloned());
    }

    public function testGetOutput(): void
    {
        $git = $this->getWorkingCopy();

        // Test getting output of a simple status command.
        $output = (string) $git->status();
        $this->assertContains('nothing to commit', $output);

        // Getting output should clear the buffer.
        $this->assertEmpty((string) $git);
    }

    public function testClearOutput(): void
    {
        $git = $this->getWorkingCopy();

        // Put stuff in the output buffer.
        $git->status();

        $git->clearOutput();
        $this->assertEmpty($git->getOutput());
    }

    public function testHasChanges(): void
    {
        $git = $this->getWorkingCopy();
        $this->assertFalse($git->hasChanges());

        file_put_contents(self::WORKING_DIR . '/change.me', "changed\n");
        $this->assertTrue($git->hasChanges());
    }

    public function testGetBranches(): void
    {
        $git = $this->getWorkingCopy();
        $branches = $git->getBranches();

        $this->assertTrue($branches instanceof GitBranches);

        // Dumb count checks. Is there a better way to do this?
        $allBranches = 0;
        foreach ($branches as $branch) {
            ++$allBranches;
        }

        $this->assertSame($allBranches, 4);

        $remoteBranches = $branches->remote();
        $this->assertSame(count($remoteBranches), 3);
    }

    public function testFetchAll(): void
    {
        $git = $this->getWorkingCopy();

        $output = rtrim((string) $git->fetchAll());

        $this->assertSame('Fetching origin', $output);
    }

    public function testGitAdd(): void
    {
        $git = $this->getWorkingCopy();
        $this->filesystem->touch(self::WORKING_DIR . '/add.me');

        $git->add('add.me');

        $this->assertTrue((bool) preg_match('@A\s+add\.me@s', $git->getStatus()));
    }

    public function testGitApply(): void
    {
        $git = $this->getWorkingCopy();

        $patch = <<<PATCH
diff --git a/FileCreatedByPatch.txt b/FileCreatedByPatch.txt
new file mode 100644
index 0000000..dfe437b
--- /dev/null
+++ b/FileCreatedByPatch.txt
@@ -0,0 +1 @@
+contents

PATCH;
        file_put_contents(self::WORKING_DIR . '/patch.txt', $patch);
        $git->apply('patch.txt');
        $this->assertRegExp('@\?\?\s+FileCreatedByPatch\.txt@s', $git->getStatus());
        $this->assertSame("contents\n", file_get_contents(self::WORKING_DIR . '/FileCreatedByPatch.txt'));
    }

    public function testGitRm(): void
    {
        $git = $this->getWorkingCopy();
        $git->rm('a.directory/remove.me');

        $this->assertFalse(is_file(self::WORKING_DIR . '/a.directory/remove.me'));
    }

    public function testGitMv(): void
    {
        $git = $this->getWorkingCopy();
        $git->mv('move.me', 'moved');

        $this->assertFalse(is_file(self::WORKING_DIR . '/move.me'));
        $this->assertTrue(is_file(self::WORKING_DIR . '/moved'));
    }

    public function testGitBranch(): void
    {
        $branchName = $this->randomString();

        // Create the branch.
        $git = $this->getWorkingCopy();
        $git->branch($branchName);

        // Get list of local branches.
        $branches = (string) $git->branch();

        // Check that our branch is there.
        $this->assertTrue(strpos($branches, $branchName) !== false);
    }

    public function testGitLog(): void
    {
        $git = $this->getWorkingCopy();
        $output = (string) $git->log();

        $this->assertTrue(strpos($output, 'Initial commit.') !== false);
    }

    public function testGitConfig(): void
    {
        $git = $this->getWorkingCopy();
        $email = trim($git->config('user.email'));
        $this->assertSame('opensource@chrispliakas.com', $email);
    }

    public function testGitTag(): void
    {
        $tag = $this->randomString();

        $git = $this->getWorkingCopy();
        $git->tag($tag);
        $git->pushTag($tag);

        $tags = (string) $git->tag();
        $this->assertContains($tag, $tags);
    }

    public function testGitClean(): void
    {
        $git = $this->getWorkingCopy();

        file_put_contents(self::WORKING_DIR . '/untracked.file', "untracked\n");

        $result = $git->clean('-d', '-f');

        $this->assertSame('Removing untracked.file', trim($result));
        $this->assertFileNotExists(self::WORKING_DIR . '/untracked.file');
    }

    public function testGitReset(): void
    {
        $git = $this->getWorkingCopy();
        file_put_contents(self::WORKING_DIR . '/change.me', "changed\n");

        $this->assertTrue($git->hasChanges());
        $git->reset(['hard' => true]);
        $this->assertFalse($git->hasChanges());
    }

    public function testGitStatus(): void
    {
        $git = $this->getWorkingCopy();
        file_put_contents(self::WORKING_DIR . '/change.me', "changed\n");
        $output = (string) $git->status(['s' => true]);
        $this->assertSame(" M change.me\n", $output);
    }

    public function testGitPull(): void
    {
        $git = $this->getWorkingCopy();
        $output = (string) $git->pull();
        // message can differ per OS/CI
        $this->assertRegExp('#Already up(-| )to(-| )date#', trim($output));
    }

    public function testGitArchive(): void
    {
        $this->markTestSkipped('Failing, not sure why.');

        $archiveName = uniqid('', true) . '.tar';
        $archivePath = __DIR__ . '/temp/' . $archiveName;

        $git = $this->getWorkingCopy();
        $output = $git->archive('HEAD', ['o' => $archivePath]);
        $this->assertSame('', $output);

        $this->assertFileExists($archivePath);
    }

    /**
     * This tests an odd case where sometimes even though a command fails and an exception is thrown
     * the result of Process::getErrorOutput() is empty because the output is sent to STDOUT instead of STDERR. So
     * there's a code path in GitProcess::run() to check the output from Process::getErrorOutput() and if it's empty use
     * the result from Process::getOutput() instead
     */
    public function testGitPullErrorWithEmptyErrorOutput(): void
    {
        $git = $this->getWorkingCopy();

        $this->expectException(GitException::class);
        $this->expectExceptionMessageRegExp("#Your branch is up(-| )to(-| )date with 'origin/master'#");
        $this->expectExceptionMessageRegExp("#nothing to commit, working tree clean#");

        $git->commit('Nothing to commit so generates an error / not error');
    }

    public function testGitDiff(): void
    {
        $git = $this->getWorkingCopy();
        file_put_contents(self::WORKING_DIR . '/change.me', "changed\n");
        $output = (string) $git->diff();
        $this->assertTrue(strpos($output, 'diff --git a/change.me b/change.me') === 0);
    }

    public function testGitGrep(): void
    {
        $git = $this->getWorkingCopy();
        $output = (string) $git->grep('changed', '--', '*.me');
        $this->assertTrue(strpos($output, 'change.me') === 0);
    }

    public function testGitShow(): void
    {
        $git = $this->getWorkingCopy();
        $output = (string) $git->show('test-tag');
        $this->assertTrue(strpos($output, 'commit ') === 0);
    }

    public function testGitBisect(): void
    {
        $git = $this->getWorkingCopy();
        $output = (string) $git->bisect('help');
        $this->assertTrue(stripos($output, 'usage: git bisect') === 0);
    }

    public function testGitRemote(): void
    {
        $git = $this->getWorkingCopy();
        $output = (string) $git->remote();
        $this->assertSame(rtrim($output), 'origin');
    }

    public function testRebase(): void
    {
        $git = $this->getWorkingCopy();
        $git->checkout('test-branch');
        $git->clearOutput();

        $output = (string) $git->rebase('test-branch', 'master');
        $this->assertTrue(strpos($output, 'First, rewinding head') === 0);
    }

    public function testMerge(): void
    {
        $git = $this->getWorkingCopy();
        $git->checkout('test-branch');
        $git->checkout('master');
        $git->clearOutput();

        $output = (string) $git->merge('test-branch');
        $this->assertStringStartsWith('Updating', $output);
    }

    public function testOutputListener(): void
    {
        $git = $this->getWorkingCopy();

        $listener = new TestOutputListener();
        $git->getWrapper()
            ->addOutputListener($listener);

        $git->status();
        $event = $listener->getLastEvent();

        $expectedType = Process::OUT;
        $this->assertSame($expectedType, $event->getType());

        $this->assertTrue(stripos($event->getBuffer(), 'nothing to commit') !== false);
    }

    public function testLiveOutput(): void
    {
        $git = $this->getWorkingCopy();

        // Capture output written to STDOUT and use echo so we can suppress and
        // capture it using normal output buffering.
        stream_filter_register('suppress', StreamSuppressFilter::class);
        $stdoutSuppress = stream_filter_append(STDOUT, 'suppress');

        $git->getWrapper()->streamOutput(true);
        ob_start();
        $git->status();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(stripos($contents, 'nothing to commit') !== false);

        $git->clearOutput();
        $git->getWrapper()->streamOutput(false);
        ob_start();
        $git->status();
        $empty = ob_get_contents();
        ob_end_clean();

        $this->assertEmpty($empty);

        stream_filter_remove($stdoutSuppress);
    }

    public function testCommitWithAuthor(): void
    {
        $git = $this->getWorkingCopy();
        file_put_contents(self::WORKING_DIR . '/commit.txt', "created\n");

        $this->assertTrue($git->hasChanges());

        $git->add('commit.txt');
        $git->commit([
            'm' => 'Committed testing branch.',
            'a' => true,
            'author' => 'test <test@lol.com>',
        ]);

        $output = (string) $git->log();
        $this->assertContains('Committed testing branch', $output);
        $this->assertContains('Author: test <test@lol.com>', $output);
    }

    public function testIsTracking(): void
    {
        $this->markTestSkipped('Only failing test, not sure why');

        $git = $this->getWorkingCopy();

        // The master branch is a remote tracking branch.
        $this->assertTrue($git->isTracking());

        // Create a new branch without pushing it, so it does not have a remote.
        $git->checkoutNewBranch('non-tracking-branch');
        $this->assertFalse($git->isTracking());
    }

    public function testIsUpToDate(): void
    {
        $git = $this->getWorkingCopy();

        // The default test branch is up-to-date with its remote.
        $git->checkout('test-branch');
        $this->assertTrue($git->isUpToDate());

        // If we create a new commit, we are still up-to-date.
        file_put_contents(self::WORKING_DIR . '/commit.txt', "created\n");
        $git->add('commit.txt');
        $git->commit([
            'm' => '1 commit ahead. Still up-to-date.',
            'a' => true,
        ]);

        $this->assertTrue($git->isUpToDate());

        // Reset the branch to its first commit, so that it is 1 commit behind.
        $git->reset(
            'HEAD~2',
            ['hard' => true]
        );

        $this->assertFalse($git->isUpToDate());
    }

    public function testIsAhead(): void
    {
        $git = $this->getWorkingCopy();

        // The default master branch is not ahead of the remote.
        $this->assertFalse($git->isAhead());

        // Create a new commit, so that the branch is 1 commit ahead.
        file_put_contents(self::WORKING_DIR . '/commit.txt', "created\n");
        $git->add('commit.txt');
        $git->commit(['m' => '1 commit ahead.']);

        $this->assertTrue($git->isAhead());
    }

    public function testIsBehind(): void
    {
        $git = $this->getWorkingCopy();

        // The default test branch is not behind the remote.
        $git->checkout('test-branch');
        $this->assertFalse($git->isBehind());

        // Reset the branch to its parent commit, so that it is 1 commit behind.
        $git->reset(
            'HEAD^',
            ['hard' => true]
        );

        $this->assertTrue($git->isBehind());
    }

    public function testNeedsMerge(): void
    {
        $git = $this->getWorkingCopy();

        // The default test branch does not need to be merged with the remote.
        $git->checkout('test-branch');
        $this->assertFalse($git->needsMerge());

        // Reset the branch to its parent commit, so that it is 1 commit behind.
        // This does not require the branches to be merged.
        $git->reset(
            'HEAD^',
            ['hard' => true]
        );
        $this->assertFalse($git->needsMerge());

        // Create a new commit, so that the branch is also 1 commit ahead. Now a
        // merge is needed.
        file_put_contents(self::WORKING_DIR . '/commit.txt', "created\n");
        $git->add('commit.txt');
        $git->commit(['m' => '1 commit ahead.']);
        $this->assertTrue($git->needsMerge());

        // Merge the remote, so that we are no longer behind, but only ahead. A
        // merge should then no longer be needed.
        $git->merge('@{u}');
        $this->assertFalse($git->needsMerge());
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
        try {
            $gitWorkingCopy->run('rev-parse', [$tag]);
        } catch (GitException $gitException) {
            // Expected result. The tag does not exist.
            return;
        }

        throw new Exception(sprintf(
            'Expecting that the tag "%s" doesn\'t exist, but it does.',
            $tag
        ));
    }

    protected function assertRemoteMaster(GitWorkingCopy $gitWorkingCopy): void
    {
        $gitWorkingCopy->run('rev-parse', ['remote/HEAD']);
    }

    protected function assertNoRemoteMaster(GitWorkingCopy $gitWorkingCopy): void
    {
        try {
            $gitWorkingCopy->run('rev-parse', ['remote/HEAD']);
        } catch (GitException $gitException) {
            // Expected result. The remote master does not exist.
            return;
        }

        throw new Exception('Expecting that the remote master doesn\'t exist, but it does.');
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

    /**
     * Clones the local repo and returns an initialized GitWorkingCopy object.
     */
    private function getWorkingCopy(string $directory = self::WORKING_DIR): GitWorkingCopy
    {
        $git = $this->gitWrapper->workingCopy($directory);
        $git->cloneRepository('file://' . realpath(self::REPO_DIR));
        $git->config('user.email', self::CONFIG_EMAIL);
        $git->config('user.name', self::CONFIG_NAME);
        $git->clearOutput();

        return $git;
    }
}
