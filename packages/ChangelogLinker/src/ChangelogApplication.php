<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class ChangelogApplication
{
    /**
     * @var string
     */
    private $repositoryLink;

    /**
     * @var WorkerInterface[]
     */
    private $workers = [];

    public function __construct(string $repositoryLink)
    {
        $this->repositoryLink = $repositoryLink;
    }

    public function addWorker(WorkerInterface $worker): void
    {
        $this->workers[] = $worker;
    }

    public function processFileAndSave(string $filePath): void
    {
        $content = $this->processFile($filePath);
        file_put_contents($filePath, $content);
    }

    public function processFile(string $filePath): string
    {
        $content = file_get_contents($filePath);

        foreach ($this->workers as $worker) {
            $content = $worker->processContent($content, $this->repositoryLink);
        }

        return $content;
    }
}
