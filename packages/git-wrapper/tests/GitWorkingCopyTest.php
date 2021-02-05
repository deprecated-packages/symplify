<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Iterator;
use Nette\Utils\Strings;
use OndraM\CiDetector\CiDetector;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitBranches;
use Symplify\GitWrapper\GitWorkingCopy;
use Symplify\GitWrapper\Tests\EventSubscriber\Source\TestGitOutputEventSubscriber;
use Symplify\GitWrapper\Tests\Source\StreamSuppressFilter;
use Symplify\GitWrapper\ValueObject\CommandName;

final class GitWorkingCopyTest extends AbstractGitWrapperTestCase
{
    /**
     * @var string
     */
    private const REMOTE_REPO_DIR = __DIR__ . '/build/tests/remote';

    /**
     * @var string
     */
    private const DIRECTORY = __DIR__ . '/build/tests/wc_init';

    /**
     * @var string
     */
    private const PATCH = <<<CODE_SAMPLE
diff --git a/FileCreatedByPatch.txt b/FileCreatedByPatch.txt
new file mode 100644
index 0000000..dfe437b
--- /dev/null
+++ b/FileCreatedByPatch.txt
@@ -0,0 +1 @@
+contents

CODE_SAMPLE;

    /**
     * @var string
     * @see https://regex101.com/r/DxaBla/1
     */
    private const ADD_ME_REGEX = '#A\\s+add\\.me#s';

    /**
     * @var string
     */
    private $currentUserName;

    /**
     * @var string
     */
    private $currentUserEmail;

    /**
     * Creates and initializes the local repository used for testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create the local repository
        $this->gitWrapper->init(self::REPO_DIR, [
            'bare' => true,
        ]);

        // cleanup, because tearDown is not working here
        if ($this->smartFileSystem->exists(self::DIRECTORY)) {
            $this->smartFileSystem->remove(self::DIRECTORY);
        }

        $gitWorkingCopy = $this->gitWrapper->cloneRepository('file://' . self::REPO_DIR, self::DIRECTORY);

        $this->storeCurrentGitUserEmail($gitWorkingCopy);

        $gitWorkingCopy->config('user.email', self::CONFIG_EMAIL);
        $gitWorkingCopy->config('user.name', self::CONFIG_NAME);

        // Create the initial structure.
        $this->smartFileSystem->dumpFile(self::DIRECTORY . '/change.me', "unchanged\n");
        $this->smartFileSystem->touch(self::DIRECTORY . '/move.me');
        $this->smartFileSystem->mkdir(self::DIRECTORY . '/a.directory', 0755);
        $this->smartFileSystem->touch(self::DIRECTORY . '/a.directory/remove.me');

        // Initial commit.
        $gitWorkingCopy->add('*');
        $gitWorkingCopy->commit('Initial commit.');
        $gitWorkingCopy->push('origin', 'master', [
            'u' => true,
        ]);

        // Create a branch, add a file.
        $branch = 'test-branch';
        $this->smartFileSystem->dumpFile(self::DIRECTORY . '/branch.txt', $branch . PHP_EOL);
        $gitWorkingCopy->checkoutNewBranch($branch);
        $gitWorkingCopy->add('branch.txt');
        $gitWorkingCopy->commit('Committed testing branch.');
        $gitWorkingCopy->push('origin', $branch, [
            'u' => true,
        ]);

        // Create a tag of the branch.
        $gitWorkingCopy->tag('test-tag');
        $gitWorkingCopy->pushTags();

        $this->smartFileSystem->remove(self::DIRECTORY);
    }

    /**
     * Removes the local repository.
     */
    protected function tearDown(): void
    {
        $this->restoreCurrentGitUserEmail();

        $this->smartFileSystem->remove(self::REPO_DIR);
        $this->smartFileSystem->remove(self::DIRECTORY);
        $this->smartFileSystem->remove(self::WORKING_DIR);
        $this->smartFileSystem->remove(self::REMOTE_REPO_DIR);
    }

    /**
     * Clones the local repo and returns an initialized GitWorkingCopy object.
     *
     * @param string $directory The directory that the repository is being cloned to, defaults to "test/wc".
     */
    public function getWorkingCopy(string $directory = self::WORKING_DIR): GitWorkingCopy
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy($directory);
        $gitWorkingCopy->cloneRepository('file://' . self::REPO_DIR);
        $gitWorkingCopy->config('user.email', self::CONFIG_EMAIL);
        $gitWorkingCopy->config('user.name', self::CONFIG_NAME);

        return $gitWorkingCopy;
    }

    public function testIsCloned(): void
    {
        $git = $this->getWorkingCopy();
        $this->assertTrue($git->isCloned());
    }

    public function testOutput(): void
    {
        $git = $this->getWorkingCopy();

        // Test getting output of a simple status command.
        $output = $git->status();
        $this->assertStringContainsString('nothing to commit', $output);
    }

    public function testHasChanges(): void
    {
        $gitWorkingCopy = $this->getWorkingCopy();
        $this->assertFalse($gitWorkingCopy->hasChanges());

        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/change.me', "changed\n");
        $this->assertTrue($gitWorkingCopy->hasChanges());
    }

    public function testGetBranches(): void
    {
        $git = $this->getWorkingCopy();
        $gitBranches = $git->getBranches();

        $this->assertInstanceOf(GitBranches::class, $gitBranches);

        // Dumb count checks. Is there a better way to do this?
        $allBranches = 0;
        foreach ($gitBranches as $branch) {
            ++$allBranches;
        }

        $this->assertSame($allBranches, 4);

        $remoteBranches = $gitBranches->remote();
        $this->assertCount(3, $remoteBranches);
    }

    public function testFetchAll(): void
    {
        $git = $this->getWorkingCopy();
        $output = rtrim($git->fetchAll());

        $this->assertSame('Fetching origin', $output);
    }

    public function testGitAdd(): void
    {
        $git = $this->getWorkingCopy();
        $this->smartFileSystem->touch(self::WORKING_DIR . '/add.me');

        $git->add('add.me');

        $match = (bool) Strings::match($git->getStatus(), self::ADD_ME_REGEX);
        $this->assertTrue($match);
    }

    public function testGitApply(): void
    {
        $git = $this->getWorkingCopy();
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/patch.txt', self::PATCH);
        $git->apply('patch.txt');

        $this->assertMatchesRegularExpression('#\?\?\\s+FileCreatedByPatch\\.txt#s', $git->getStatus());
        $this->assertStringEqualsFile(self::WORKING_DIR . '/FileCreatedByPatch.txt', "contents\n");
    }

    public function testGitRm(): void
    {
        $gitWorkingCopy = $this->getWorkingCopy();
        $gitWorkingCopy->rm('a.directory/remove.me');

        $this->assertFileDoesNotExist(self::WORKING_DIR . '/a.directory/remove.me');
    }

    public function testGitMv(): void
    {
        $gitWorkingCopy = $this->getWorkingCopy();
        $gitWorkingCopy->mv('move.me', 'moved');

        $this->assertFileDoesNotExist(self::WORKING_DIR . '/move.me');
        $this->assertFileExists(self::WORKING_DIR . '/moved');
    }

    public function testGitBranch(): void
    {
        $branchName = $this->randomString();

        // Create the branch.
        $git = $this->getWorkingCopy();
        $git->branch($branchName);

        // Get list of local branches.
        $branches = $git->branch();

        // Check that our branch is there.
        $this->assertStringContainsString($branchName, $branches);
    }

    public function testGitLog(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->log();
        $this->assertStringContainsString('Initial commit.', $output);
    }

    public function testGitConfig(): void
    {
        $git = $this->getWorkingCopy();
        $email = rtrim($git->config('user.email'));
        $this->assertSame('testing@email.com', $email);
    }

    public function testGitTag(): void
    {
        $tag = $this->randomString();

        $git = $this->getWorkingCopy();
        $git->tag($tag);
        $git->pushTag($tag);

        $tags = $git->tag();
        $this->assertStringContainsString($tag, $tags);
    }

    public function testGitClean(): void
    {
        $git = $this->getWorkingCopy();

        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/untracked.file', "untracked\n");

        $result = $git->clean('-d', '-f');
        $this->assertSame('Removing untracked.file' . PHP_EOL, $result);

        $this->assertFileDoesNotExist(self::WORKING_DIR . '/untracked.file');
    }

    public function testGitReset(): void
    {
        $git = $this->getWorkingCopy();
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/change.me', "changed\n");

        $this->assertTrue($git->hasChanges());
        $git->reset([
            'hard' => true,
        ]);
        $this->assertFalse($git->hasChanges());
    }

    public function testGitStatus(): void
    {
        $git = $this->getWorkingCopy();
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/change.me', "changed\n");
        $output = $git->status([
            's' => true,
        ]);
        $this->assertSame(" M change.me\n", $output);
    }

    public function testGitPull(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->pull();
        $cleanOutput = rtrim($output);

        $this->assertMatchesRegularExpression("/^Already up[- ]to[ -]date\.$/", $cleanOutput);
    }

    public function testGitArchive(): void
    {
        $archiveName = uniqid() . '.tar';
        $archivePath = '/tmp/' . $archiveName;
        $git = $this->getWorkingCopy();
        $output = $git->archive('HEAD', [
            'o' => $archivePath,
        ]);
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
        $this->expectExceptionMessageMatches("#Your branch is up[- ]to[- ]date with 'origin\\/master'.#");

        $git = $this->getWorkingCopy();
        $git->commit('Nothing to commit so generates an error / not error');
    }

    public function testGitDiff(): void
    {
        $git = $this->getWorkingCopy();
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/change.me', "changed\n");
        $output = $git->diff();

        $this->assertStringStartsWith('diff --git a/change.me b/change.me', $output);
    }

    public function testGitGrep(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->grep('changed', '--', '*.me');

        $this->assertStringStartsWith('change.me', $output);
    }

    public function testGitShow(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->show('test-tag');

        $this->assertStringStartsWith('commit ', $output);
    }

    public function testGitBisect(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->bisect('help');

        $this->assertStringStartsWith('usage: git bisect', $output);
    }

    public function testGitRemote(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->remote();
        $cleanOutput = rtrim($output);

        $this->assertSame('origin', $cleanOutput);
    }

    public function testRebase(): void
    {
        $git = $this->getWorkingCopy();
        $git->checkout('test-branch');

        $output = $git->rebase('test-branch', [
            'onto' => 'master',
        ]);

        // nothing to rebase
        $this->assertSame('', $output);
    }

    public function testMerge(): void
    {
        $git = $this->getWorkingCopy();
        $git->checkout('test-branch');
        $git->checkout('master');

        $output = $git->merge('test-branch');

        $this->assertStringStartsWith('Updating ', $output);
    }

    public function testOutputListener(): void
    {
        $git = $this->getWorkingCopy();

        $testGitOutputEventSubscriber = new TestGitOutputEventSubscriber();
        $gitWrapper = $git->getWrapper();
        $gitWrapper->addOutputEventSubscriber($testGitOutputEventSubscriber);

        $git->status();

        $gitOutputEvent = $testGitOutputEventSubscriber->getLastEvent();
        $this->assertSame(Process::OUT, $gitOutputEvent->getType());

        $this->assertStringContainsString('nothing to commit', $gitOutputEvent->getBuffer());
    }

    public function testLiveOutput(): void
    {
        $git = $this->getWorkingCopy();

        // Capture output written to STDOUT and use echo so we can suppress and
        // capture it using normal output buffering.
        stream_filter_register('suppress', StreamSuppressFilter::class);
        /** @var resource $stdoutSuppress */
        $stdoutSuppress = stream_filter_append(STDOUT, 'suppress');

        $gitWrapper = $git->getWrapper();
        $gitWrapper->streamOutput(true);

        ob_start();
        $git->status();
        $contents = ob_get_contents();
        ob_end_clean();

        /** @var string $contents */
        $this->assertStringContainsString('nothing to commit', $contents);

        $gitWrapper = $git->getWrapper();
        $gitWrapper->streamOutput(false);

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
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/commit.txt', "created\n");

        $this->assertTrue($git->hasChanges());

        $git->add('commit.txt');
        $git->commit([
            'm' => 'Committed testing branch.',
            'a' => true,
            'author' => 'test <test@lol.com>',
        ]);

        $output = $git->log();
        $this->assertStringContainsString('Committed testing branch', $output);
        $this->assertStringContainsString('Author: test <test@lol.com>', $output);
    }

    public function testIsTracking(): void
    {
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
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/commit.txt', "created\n");
        $git->add('commit.txt');
        $git->commit([
            'm' => '1 commit ahead. Still up-to-date.',
            'a' => true,
        ]);
        $this->assertTrue($git->isUpToDate());

        // Reset the branch to its first commit, so that it is 1 commit behind.
        $git->reset('HEAD~2', [
            'hard' => true,
        ]);

        $this->assertFalse($git->isUpToDate());
    }

    public function testIsAhead(): void
    {
        $git = $this->getWorkingCopy();

        // The default master branch is not ahead of the remote.
        $this->assertFalse($git->isAhead());

        // Create a new commit, so that the branch is 1 commit ahead.
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/commit.txt', "created\n");
        $git->add('commit.txt');
        $git->commit([
            'm' => '1 commit ahead.',
        ]);

        $this->assertTrue($git->isAhead());
    }

    public function testIsBehind(): void
    {
        $git = $this->getWorkingCopy();

        // The default test branch is not behind the remote.
        $git->checkout('test-branch');
        $this->assertFalse($git->isBehind());

        // Reset the branch to its parent commit, so that it is 1 commit behind.
        $git->reset('HEAD^', [
            'hard' => true,
        ]);

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
        $git->reset('HEAD^', [
            'hard' => true,
        ]);
        $this->assertFalse($git->needsMerge());

        // Create a new commit, so that the branch is also 1 commit ahead. Now a
        // merge is needed.
        $this->smartFileSystem->dumpFile(self::WORKING_DIR . '/commit.txt', "created\n");
        $git->add('commit.txt');
        $git->commit([
            'm' => '1 commit ahead.',
        ]);
        $this->assertTrue($git->needsMerge());

        // Merge the remote, so that we are no longer behind, but only ahead. A
        // merge should then no longer be needed.
        $git->merge('@{u}');
        $this->assertFalse($git->needsMerge());
    }

    /**
     * @dataProvider provideDataForRemote()
     * @param mixed[] $options
     */
    public function testAddRemote(array $options, ?string $gitTag, ?string $noGitTag): void
    {
        $this->createRemote();
        $gitWorkingCopy = $this->getWorkingCopy();
        $gitWorkingCopy->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR, $options);
        $this->assertTrue($gitWorkingCopy->hasRemote('remote'));

        if ($gitTag !== null) {
            $this->assertGitTag($gitWorkingCopy, $gitTag);
        }

        if ($noGitTag !== null) {
            $this->assertNoGitTag($gitWorkingCopy, $noGitTag);
        }

        $this->assertNoRemoteMaster($gitWorkingCopy);
    }

    /**
     * @dataProvider provideDataNoRemoteBranches()
     * @param mixed[] $options
     * @param string[] $noRemoteBranches
     */
    public function testNoRemoteBranches(array $options, array $noRemoteBranches): void
    {
        $this->createRemote();

        $gitWorkingCopy = $this->getWorkingCopy();
        $gitWorkingCopy->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR, $options);
        $this->assertTrue($gitWorkingCopy->hasRemote('remote'));

        foreach ($noRemoteBranches as $noRemoteBranch) {
            $this->assertNoRemoteBranch($gitWorkingCopy, $noRemoteBranch);
        }
    }

    public function provideDataNoRemoteBranches(): Iterator
    {
        yield [[], ['remote/master', 'remote/remote-branch']];

        yield [
            [
                '-f' => true,
                '-t' => ['master'],
            ],
            ['remote/remote-branch'],
        ];

        yield [
            [
                '-f' => true,
                '-t' => ['master'],
                '--tags' => true,
            ],
            ['remote/remote-branch'],
        ];
    }

    /**
     * @dataProvider provideDataRemoteBranches()
     * @param mixed[] $options
     * @param string[] $remoteBranches
     */
    public function testRemoteBranches(array $options, array $remoteBranches): void
    {
        $this->createRemote();

        $gitWorkingCopy = $this->getWorkingCopy();
        $gitWorkingCopy->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR, $options);
        $this->assertTrue($gitWorkingCopy->hasRemote('remote'));

        foreach ($remoteBranches as $remoteBranch) {
            $this->assertRemoteBranch($gitWorkingCopy, $remoteBranch);
        }
    }

    public function provideDataRemoteBranches(): Iterator
    {
        yield [
            [
                '-f' => true,
            ],
            ['remote/master', 'remote/remote-branch'],
        ];

        yield [
            [
                '-f' => true,
                '-t' => ['master'],
            ],
            ['remote/master'],
        ];

        yield [
            // The --no-tags options should omit importing tags.
            [
                '-f' => true,
                '--no-tags' => true,
            ],
            ['remote/master', 'remote/remote-branch'],
        ];

        yield [
            [
                '-f' => true,
                '-t' => ['master'],
                '--tags' => true,
            ],
            ['remote/master'],
        ];

        yield [
            [
                '-f' => true,
                '-m' => 'remote-branch',
            ],
            ['remote/master', 'remote/remote-branch'],
        ];
    }

    public function provideDataForRemote(): \Iterator
    {
        yield [
            // Test default options: nothing is fetched.
            [], null, 'remote-tag',
        ];

        // The fetch option should retrieve the remote branches and tags,
        // but not set up a master branch.
        yield [
            [
                '-f' => true,
            ],
            'remote-tag',
            null,
        ];

        // The --no-tags options should omit importing tags.
        yield [
            [
                '-f' => true,
                '--no-tags' => true,
            ],
            null,
            'remote-tag',
        ];

        // The -t option should limit the remote branches that are imported.
        // By default git fetch only imports the tags of the fetched
        // branches. No tags were added to the master branch, so the tag
        // should not be imported.
        yield [
            [
                '-f' => true,
                '-t' => ['master'],
            ],
            null,
            'remote-tag',
        ];

        // The -t option in combination with the --tags option should fetch
        // all tags, so now the tag should be there.
        yield [
            [
                '-f' => true,
                '-t' => ['master'],
                '--tags' => true,
            ],
            'remote-tag',
            null,
        ];
    }

    /**
     * The -m option should set up a remote master branch.
     * @doesNotPerformAssertions
     */
    public function testRemoteMaster(): void
    {
        $this->createRemote();

        $gitWorkingCopy = $this->getWorkingCopy();

        $options = [
            '-f' => true,
            '-m' => 'remote-branch',
        ];

        $gitWorkingCopy->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR, $options);

        $gitWorkingCopy->run(CommandName::REV_PARSE, ['remote/HEAD']);
        $gitWorkingCopy->run(CommandName::REV_PARSE, ['remote-tag']);
    }

    public function testRemoveRemote(): void
    {
        $this->createRemote();
        $gitWorkingCopy = $this->getWorkingCopy();
        $gitWorkingCopy->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR);

        $hasRemote = $gitWorkingCopy->hasRemote('remote');
        $this->assertTrue($hasRemote);

        // The remote "remote" should be gone
        $gitWorkingCopy->removeRemote('remote');

        $hasRemote = $gitWorkingCopy->hasRemote('remote');
        $this->assertFalse($hasRemote);
    }

    public function testHasRemote(): void
    {
        $this->createRemote();
        $gitWorkingCopy = $this->getWorkingCopy();

        // The remote should be absent before it is added.
        $hasRemote = $gitWorkingCopy->hasRemote('remote');
        $this->assertFalse($hasRemote);

        $gitWorkingCopy->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR);

        // The remote should be present after it is added.
        $hasRemote = $gitWorkingCopy->hasRemote('remote');
        $this->assertTrue($hasRemote);
    }

    public function testGetRemote(): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();
        $path = 'file://' . self::REMOTE_REPO_DIR;
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
        $git->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR);
        $remotes = $git->getRemotes();
        $this->assertArrayHasKey('origin', $remotes);
        $this->assertArrayHasKey('remote', $remotes);
    }

    /**
     * @dataProvider getRemoteUrlDataProvider
     */
    public function testGetRemoteUrl(string $remote, string $operation, string $expectedRemoteUrl): void
    {
        $this->createRemote();
        $git = $this->getWorkingCopy();
        $git->addRemote('remote', 'file://' . self::REMOTE_REPO_DIR);

        $resolveRemoveUrl = $git->getRemoteUrl($remote, $operation);
        $this->assertSame('file://' . $expectedRemoteUrl, $resolveRemoveUrl);
    }

    /**
     * @return Iterator<string[]>
     */
    public function getRemoteUrlDataProvider(): Iterator
    {
        yield ['origin', 'fetch', self::REPO_DIR];
        yield ['origin', 'push', self::REPO_DIR];
        yield ['remote', 'fetch', self::REMOTE_REPO_DIR];
        yield ['remote', 'push', self::REMOTE_REPO_DIR];
    }

    private function assertGitTag(GitWorkingCopy $gitWorkingCopy, string $tag): void
    {
        $gitWorkingCopy->run(CommandName::REV_PARSE, [$tag]);
    }

    private function assertNoGitTag(GitWorkingCopy $gitWorkingCopy, string $tag): void
    {
        try {
            $gitWorkingCopy->run(CommandName::REV_PARSE, [$tag]);
        } catch (GitException $gitException) {
            // Expected result. The tag does not exist.
            return;
        }

        throw new GitException(sprintf('Tag "%s" should not exist', $tag));
    }

    private function assertNoRemoteMaster(GitWorkingCopy $gitWorkingCopy): void
    {
        try {
            $gitWorkingCopy->run(CommandName::REV_PARSE, ['remote/HEAD']);
        } catch (GitException $gitException) {
            // Expected result. The remote master does not exist.
            return;
        }

        throw new GitException('Branch `master` should not exist');
    }

    private function assertRemoteBranch(GitWorkingCopy $gitWorkingCopy, string $branch): void
    {
        $gitBranches = $gitWorkingCopy->getBranches();

        $remoteBranches = $gitBranches->remote();
        $remoteBranchNames = array_flip($remoteBranches);

        $this->assertArrayHasKey($branch, $remoteBranchNames);
    }

    private function assertNoRemoteBranch(GitWorkingCopy $gitWorkingCopy, string $branch): void
    {
        $gitBranches = $gitWorkingCopy->getBranches();

        $remoteBranches = $gitBranches->remote();
        $remoteBranchNames = array_flip($remoteBranches);

        $this->assertArrayNotHasKey($branch, $remoteBranchNames);
    }

    private function createRemote(): void
    {
        // Create a clone of the working copy that will serve as a remote.
        $gitWorkingCopy = $this->gitWrapper->cloneRepository('file://' . self::REPO_DIR, self::REMOTE_REPO_DIR);
        $gitWorkingCopy->config('user.email', self::CONFIG_EMAIL);
        $gitWorkingCopy->config('user.name', self::CONFIG_NAME);

        // Make a change to the remote repo.
        $this->smartFileSystem->dumpFile(self::REMOTE_REPO_DIR . '/remote.file', "remote code\n");
        $gitWorkingCopy->add('*');
        $gitWorkingCopy->commit('Remote change.');

        // Create a branch.
        $branch = 'remote-branch';
        $this->smartFileSystem->dumpFile(self::REMOTE_REPO_DIR . '/remote-branch.txt', $branch . PHP_EOL);
        $gitWorkingCopy->checkoutNewBranch($branch);
        $gitWorkingCopy->add('*');
        $gitWorkingCopy->commit('Commit remote testing branch.');

        // Create a tag.
        $gitWorkingCopy->tag('remote-tag');
    }

    private function storeCurrentGitUserEmail(GitWorkingCopy $gitWorkingCopy): void
    {
        // relevant only locally
        $ciDetector = new CiDetector();
        if ($ciDetector->isCiDetected()) {
            return;
        }

        // prevent local user.* override
        $this->currentUserEmail = $gitWorkingCopy->config('user.email');
        $this->currentUserName = $gitWorkingCopy->config('user.name');
    }

    private function restoreCurrentGitUserEmail(): void
    {
        // relevant only locally
        $ciDetector = new CiDetector();
        if ($ciDetector->isCiDetected()) {
            return;
        }

        $gitWorkingCopy = $this->gitWrapper->workingCopy(self::REPO_DIR);
        $gitWorkingCopy->config('user.email', $this->currentUserEmail);
        $gitWorkingCopy->config('user.name', $this->currentUserName);
    }
}
