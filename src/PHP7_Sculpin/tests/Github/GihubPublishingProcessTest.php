<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Github;

use Exception;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Github\GihubPublishingProcess;

final class GihubPublishingProcessTest extends TestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__.'/GithubPublishingProcessSource';

    /**
     * @var GihubPublishingProcess
     */
    private $githubPublishingProcess;

    protected function setUp()
    {
        $this->githubPublishingProcess = new GihubPublishingProcess();
    }

    public function testSetupTravisIdentityToGit()
    {
        $this->githubPublishingProcess->setupTravisIdentityToGit();
    }

    /**
     * @expectedException Exception
     */
    public function testPushDirectoryContentToRepositoryForNonExistingRepository()
    {
        $this->githubPublishingProcess->pushDirectoryContentToRepository('missing directory', '');
    }

    public function testPushDirectoryContentToRepository()
    {
        $this->assertFileNotExists($this->outputDirectory.'/.git');

        $this->githubPublishingProcess->pushDirectoryContentToRepository($this->outputDirectory, '');

        $this->assertFileExists($this->outputDirectory.'/.git');
    }

    protected function tearDown()
    {
        FileSystem::delete($this->outputDirectory.'/.git');
    }
}
