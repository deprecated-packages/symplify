<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use function Safe\usort;

final class ChangelogLinker
{
    /**
     * @var WorkerInterface[]
     */
    private $workers = [];

    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var VersionsAnalyzer
     */
    private $versionsAnalyzer;

    public function __construct(
        LinksAnalyzer $linksAnalyzer,
        LinkAppender $linkAppender,
        VersionsAnalyzer $versionsAnalyzer
    ) {
        $this->linksAnalyzer = $linksAnalyzer;
        $this->linkAppender = $linkAppender;
        $this->versionsAnalyzer = $versionsAnalyzer;
    }

    public function addWorker(WorkerInterface $worker): void
    {
        $this->workers[] = $worker;
    }

    public function processContent(string $content): string
    {
        $this->versionsAnalyzer->analyzeContent($content);
        $this->linksAnalyzer->analyzeContent($content);

        foreach ($this->getSortedWorkers() as $worker) {
            $content = $worker->processContent($content);
        }

        return $content;
    }

    public function processContentWithLinkAppends(string $content): string
    {
        $content = $this->processContent($content);

        return $this->appendLinksToContentIfAny($content);
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

    private function appendLinksToContentIfAny(string $content): string
    {
        if ($this->linkAppender->getLinksToAppend()) {
            return $content . PHP_EOL . implode(PHP_EOL, $this->linkAppender->getLinksToAppend());
        }

        return $content;
    }
}
