<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Github;

use Nette\Utils\FileSystem;
use Symplify\Statie\Github\GithubPublishingProcess;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Throwable;

final class GithubPublishingProcessTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'GithubPublishingProcessSource';

    /**
     * @var GithubPublishingProcess
     */
    private $githubPublishingProcess;

    protected function setUp(): void
    {
        $this->githubPublishingProcess = $this->container->get(GithubPublishingProcess::class);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');
    }

    public function testPushDirectoryContentToRepositoryForNonExistingRepository(): void
    {
        $this->expectException(Throwable::class);
        $this->githubPublishingProcess->pushDirectoryContentToRepository('missing directory', '', '');
    }
}
