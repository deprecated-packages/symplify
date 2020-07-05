<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Github;

use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

/**
 * @group api
 */
final class GithubApiTest extends AbstractKernelTestCase
{
    /**
     * @var GithubApi
     */
    private $githubApi;

    protected function setUp(): void
    {
        $this->bootKernel(ChangelogLinkerKernel::class);

        $this->githubApi = self::$container->get(GithubApi::class);
    }

    public function testGetUnmergedPrsSinceId(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Can be tested only with PRs in monorepo, not in split where are no PRs.');
        }

        $mergedPullRequests = $this->githubApi->getMergedPullRequestsSinceId(1000);

        $mergedPullRequestCount = count($mergedPullRequests);
        $this->assertGreaterThanOrEqual(45, $mergedPullRequestCount);
    }
}
