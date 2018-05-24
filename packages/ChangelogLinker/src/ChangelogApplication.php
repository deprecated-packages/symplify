<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Analyzer\LinkedVersionsAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class ChangelogApplication
{
    /**
     * @var WorkerInterface[]
     */
    private $workers = [];
    /**
     * @var LinkedVersionsAnalyzer
     */
    private $linkedVersionsAnalyzer;

    public function __construct(LinkedVersionsAnalyzer $linkedVersionsAnalyzer)
    {
        $this->linkedVersionsAnalyzer = $linkedVersionsAnalyzer;
    }

    public function addWorker(WorkerInterface $worker): void
    {
        $this->workers[] = $worker;
    }

    public function processFile(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $this->linkedVersionsAnalyzer->analyzeContent($content);

        foreach ($this->getSortedWorkers() as $worker) {
            $content = $worker->processContent($content);
        }

        return $content;
    }

    public function processFileWithSingleWorker(string $filePath, string $workerClass): string
    {
        $content = file_get_contents($filePath);
        $this->linkedVersionsAnalyzer->analyzeContent($content);

        foreach ($this->getSortedWorkers() as $worker) {
            if ($worker instanceof $workerClass) {
                return $worker->processContent($content);
            }
        }

        return $content;
    }

    /**
     * @return WorkerInterface[]
     */
    private function getSortedWorkers(): array
    {
        usort($this->workers, function (WorkerInterface $firstWorker, WorkerInterface $secondWorker): bool {
            return $firstWorker->getPriority() < $secondWorker->getPriority();
        });

        return $this->workers;
    }
}
