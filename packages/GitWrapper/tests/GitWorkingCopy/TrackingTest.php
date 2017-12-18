<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\GitWorkingCopy;

final class TrackingTest extends AbstractGitWorkingCopyTestCase
{
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
}
