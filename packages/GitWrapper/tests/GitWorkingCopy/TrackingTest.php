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
}
