<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class ChangelogApplication
{
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

    public function processFile(string $filePath): string
    {
        $content = file_get_contents($filePath);

        foreach ($this->workers as $worker) {
            $content = $worker->processContent($content);
        }

        return $content;
    }

    public function processFileWithSingleWorker(string $filePath, string $workerClass): string
    {
        $content = file_get_contents($filePath);

        foreach ($this->workers as $worker) {
            if ($worker instanceof $workerClass) {
                return $worker->processContent($content);
            }
        }

        return $content;
    }
}
