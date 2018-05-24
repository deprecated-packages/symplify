<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Analyzer\LinkedVersionsAnalyzer;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
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

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var VersionsAnalyzer
     */
    private $versionsAnalyzer;

    public function __construct(
        LinkedVersionsAnalyzer $linkedVersionsAnalyzer,
        LinkAppender $linkAppender,
        VersionsAnalyzer $versionsAnalyzer
    ) {
        $this->linkedVersionsAnalyzer = $linkedVersionsAnalyzer;
        $this->linkAppender = $linkAppender;
        $this->versionsAnalyzer = $versionsAnalyzer;
    }

    public function addWorker(WorkerInterface $worker): void
    {
        $this->workers[] = $worker;
    }

    public function processFile(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $this->versionsAnalyzer->analyzeContent($content);
        $this->linkedVersionsAnalyzer->analyzeContent($content);

        foreach ($this->getSortedWorkers() as $worker) {
            $content = $worker->processContent($content);
        }

        return $content . PHP_EOL . implode(PHP_EOL, $this->linkAppender->getLinksToAppend());
    }

    public function processFileWithSingleWorker(string $filePath, string $workerClass): string
    {
        $content = file_get_contents($filePath);
        $this->versionsAnalyzer->analyzeContent($content);
        $this->linkedVersionsAnalyzer->analyzeContent($content);

        foreach ($this->getSortedWorkers() as $worker) {
            if ($worker instanceof $workerClass) {
                $content = $worker->processContent($content);
            }
        }

        if ($this->linkAppender->getLinksToAppend()) {
            return $content . PHP_EOL . implode(PHP_EOL, $this->linkAppender->getLinksToAppend());
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
