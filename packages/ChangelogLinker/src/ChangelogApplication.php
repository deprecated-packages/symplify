<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Configuration\ChangelogLinkerConfiguration;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class ChangelogApplication
{
    /**
     * @var ChangelogLinkerConfiguration
     */
    private $changelogLinkerConfiguration;

    public function __construct(ChangelogLinkerConfiguration $changelogLinkerConfiguration)
    {
        $this->changelogLinkerConfiguration = $changelogLinkerConfiguration;
    }

    /**
     * @var WorkerInterface[]
     */
    private $workers = [];

    /**
     * @todo with some order?
     */
    public function addWorker(WorkerInterface $worker): void
    {
        $this->workers[] = $worker;
    }

    public function processFile(string $filePath, string $repositoryUrl): string
    {
        $this->changelogLinkerConfiguration->setRepositoryLink($repositoryUrl);

        $content = file_get_contents($filePath);

        foreach ($this->workers as $worker) {
            $content = $worker->processContent($content);
        }

        return $content;
    }

    public function processFileWithSingleWorker(string $filePath, string $repositoryUrl, string $workerClass): string
    {
        $this->changelogLinkerConfiguration->setRepositoryLink($repositoryUrl);

        $content = file_get_contents($filePath);

        foreach ($this->workers as $worker) {
            if ($worker instanceof $workerClass) {
                return $worker->processContent($content);
            }
        }

        return $content;
    }
}
