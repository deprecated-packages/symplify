<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

/**
 * @see \Symplify\ChangelogLinker\Tests\ChangelogLinker\ChangelogLinkerTest
 */
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

    /**
     * @param WorkerInterface[] $workers
     */
    public function __construct(
        LinksAnalyzer $linksAnalyzer,
        LinkAppender $linkAppender,
        VersionsAnalyzer $versionsAnalyzer,
        array $workers = []
    ) {
        $this->linksAnalyzer = $linksAnalyzer;
        $this->linkAppender = $linkAppender;
        $this->versionsAnalyzer = $versionsAnalyzer;
        $this->workers = $this->sortWorkers($workers);
    }

    public function processContent(string $content): string
    {
        $this->versionsAnalyzer->analyzeContent($content);
        $this->linksAnalyzer->analyzeContent($content);

        foreach ($this->workers as $worker) {
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
     * @param WorkerInterface[] $workers
     * @return WorkerInterface[]
     */
    private function sortWorkers(array $workers): array
    {
        usort($workers, function (WorkerInterface $firstWorker, WorkerInterface $secondWorker): int {
            return $secondWorker->getPriority() <=> $firstWorker->getPriority();
        });

        return $workers;
    }

    private function appendLinksToContentIfAny(string $content): string
    {
        $linksToAppend = $this->linkAppender->getLinksToAppend();
        if ($linksToAppend !== []) {
            $content = rtrim($content) . PHP_EOL;
            $content .= $this->linkAppender->isExistingLinks() ? '' : PHP_EOL;
            $content .= implode(PHP_EOL, $linksToAppend) . PHP_EOL;
        }

        return $content;
    }
}
