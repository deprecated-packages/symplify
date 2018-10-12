<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Github;

use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\ChangelogLinker\Tests\AbstractContainerAwareTestCase;

/**
 * @group api
 */
final class GithubApiTest extends AbstractContainerAwareTestCase
{
    /**
     * @var GithubApi
     */
    private $githubApi;

    protected function setUp(): void
    {
        $this->githubApi = $this->container->get(GithubApi::class);
    }

    public function testGetUnmergedPrsSinceId(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Can be tested only with PRs in monorepo, not in split where are no PRs.');
        }

        $mergedPullRequests = $this->githubApi->getMergedPullRequestsSinceId(1000);
        $this->assertGreaterThanOrEqual(45, count($mergedPullRequests));
    }
}
