<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests;

use Symplify\ChangelogLinker\ChangelogApplication;

abstract class AbstractWorkerTestCase extends AbstractContainerAwareTestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $this->changelogApplication = $this->container->get(ChangelogApplication::class);
    }

    protected function doProcess(string $originalFile, string $workerClass): string
    {
        return $this->changelogApplication->processFileWithSingleWorker(
            $originalFile,
            'https://github.com/Symplify/Symplify',
            $workerClass
        );
    }
}
