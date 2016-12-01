<?php

declare(strict_types=1);

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

    protected function setUp()
    {
        $this->githubPublishingProcess = new GihubPublishingProcess();
    }

    /**
     * @expectedException \Exception
     */
    public function testPushDirectoryContentToRepositoryForNonExistingRepository()
    {
        $this->githubPublishingProcess->pushDirectoryContentToRepository('missing directory', '');
    }

    /**
     * @slow
     */
    public function testPushDirectoryContentToRepository()
    {
        $this->markTestSkipped('Prepare demo repository with token first.');

        $this->assertFileNotExists($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');

        $this->githubPublishingProcess->pushDirectoryContentToRepository(
            $this->outputDirectory,
            'https://github.com/TomasVotruba/tomasvotruba.cz'
        );

        $this->assertFileExists($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');
    }

    protected function tearDown()
    {
        if (getenv('APPVEYOR')) { // AppVeyor doesn't have rights to delete
            return;
        }

        FileSystem::delete($this->outputDirectory . DIRECTORY_SEPARATOR . '.git');
    }
}
