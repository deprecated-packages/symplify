<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\GitWorkingCopy;

use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitBranches;
use Symplify\GitWrapper\Tests\EventSubscriber\Source\TestOutputSubscriber;

final class GitWorkingCopyTest extends AbstractGitWorkingCopyTestCase
{
    public function testIsCloned(): void
    {
        $git = $this->getWorkingCopy();
        $this->assertTrue($git->isCloned());
    }

    public function testGetOutput(): void
    {
        $git = $this->getWorkingCopy();

        // Test getting output of a simple status command.
        $output = $git->status();
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
        $this->assertCount(3, $remoteBranches);
    }

    public function testFetchAll(): void
    {
        $git = $this->getWorkingCopy();

        $this->assertSame('Fetching origin', $git->fetchAll());
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
        $this->assertStringEqualsFile(self::WORKING_DIR . '/FileCreatedByPatch.txt', "contents\n");
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

        $this->assertFileNotExists(self::WORKING_DIR . '/move.me');
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
        $this->assertContains($branchName, $branches);
    }

    public function testGitLog(): void
    {
        $git = $this->getWorkingCopy();

        $this->assertContains('Initial commit.', $git->log());
    }

    public function testGitTag(): void
    {
        $git = $this->getWorkingCopy();
        $git->tag('v3.0');
        $git->pushTag('v3.0');

        $this->assertContains('v3.0', $git->tag());
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
        $output = $git->status(['s' => true]);
        $this->assertSame(" M change.me\n", $output);
    }

    public function testGitPull(): void
    {
        $git = $this->getWorkingCopy();
        $output = $git->pull();
        // message can differ per OS/CI
        $this->assertRegExp('#Already up(-| )to(-| )date#', trim($output));
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
        $this->expectExceptionMessageRegExp('#nothing to commit, working tree clean#');

        $git->commit('Nothing to commit so generates an error / not error');
    }

    public function testGitDiff(): void
    {
        $git = $this->getWorkingCopy();
        file_put_contents(self::WORKING_DIR . '/change.me', "changed\n");
        $output = $git->diff();
        $this->assertContains('diff --git a/change.me b/change.me', $output);
    }

    public function testGitGrep(): void
    {
        $git = $this->getWorkingCopy();
        $this->assertContains('change.me', $git->grep('changed', '--', '*.me'));
        $this->assertContains('commit ', $git->show('test-tag'));
        $this->assertContains('usage: git bisect', $git->bisect('help'));
        $this->assertContains('origin', $git->remote());
        $this->assertContains('opensource@chrispliakas.com', $git->config('user.email'));
    }

    public function testRebase(): void
    {
        $git = $this->getWorkingCopy();
        $git->checkout('test-branch');
        $git->clearOutput();

        $output = $git->rebase('test-branch', 'master');
        $this->assertContains('First, rewinding head', $output);
    }

    public function testMerge(): void
    {
        $git = $this->getWorkingCopy();
        $git->checkout('test-branch');
        $git->checkout('master');
        $git->clearOutput();

        $output = $git->merge('test-branch');
        $this->assertStringStartsWith('Updating', $output);
    }

    public function testOutputListener(): void
    {
        $git = $this->getWorkingCopy();

        $subscriber = new TestOutputSubscriber();
        $this->eventDispatcher->addSubscriber($subscriber);
        $git->status();

        $event = $subscriber->getLastEvent();
        $this->assertSame(Process::OUT, $event->getType());

        $this->assertContains('nothing to commit', $event->getBuffer());
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

        $output = $git->log();
        $this->assertContains('Committed testing branch', $output);
        $this->assertContains('Author: test <test@lol.com>', $output);
    }
}
