<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Github;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Github\GihubPublishingProcess;

final class GihubPublishingProcessTest extends TestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'GithubPublishingProcessSource';

    /**
     * @var GihubPublishingProcess
     */
    private $githubPublishingProcess;

    protected function setUp(): void
    {
        $this->githubPublishingProcess = new GihubPublishingProcess;
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');
    }

    /**
     * @expectedException \Exception
     */
    public function testPushDirectoryContentToRepositoryForNonExistingRepository(): void
    {
        $this->githubPublishingProcess->pushDirectoryContentToRepository('missing directory', '', '');
    }

    public function testPushDirectoryContentToRepository(): void
    {
        $this->markTestSkipped('Prepare demo repository with token first.');

        $this->assertFileNotExists($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');

        $this->githubPublishingProcess->pushDirectoryContentToRepository(
            $this->outputDirectory,
            'https://github.com/TomasVotruba/tomasvotruba.cz',
            'gh-pages'
        );

        $this->assertFileExists($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');
    }
}
