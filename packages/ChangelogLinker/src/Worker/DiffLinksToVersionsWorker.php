<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
use Symplify\ChangelogLinker\Configuration\Configuration;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;

final class DiffLinksToVersionsWorker implements WorkerInterface
{
    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var VersionsAnalyzer
     */
    private $versionsAnalyzer;

    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Configuration $configuration,
        LinkAppender $linkAppender,
        VersionsAnalyzer $versionsAnalyzer,
        LinksAnalyzer $linksAnalyzer
    ) {
        $this->linkAppender = $linkAppender;
        $this->versionsAnalyzer = $versionsAnalyzer;
        $this->linksAnalyzer = $linksAnalyzer;
        $this->configuration = $configuration;
    }

    public function processContent(string $content): string
    {
        foreach ($this->versionsAnalyzer->getVersions() as $index => $version) {
            if ($this->shouldSkip($version)) {
                continue;
            }

            $link = sprintf(
                '[%s]: %s/compare/%s...%s',
                $version,
                $this->configuration->getRepositoryUrl(),
                $this->versionsAnalyzer->getVersions()[$index + 1],
                $version
            );

            $this->linkAppender->add($version, $link);
        }

        // append new links to the file
        return $content;
    }

    public function getPriority(): int
    {
        return 800;
    }

    private function shouldSkip(string $version): bool
    {
        if ($this->linksAnalyzer->hasLinkedId($version)) {
            return true;
        }

        return ! $this->versionsAnalyzer->isLastVersion($version);
    }
}
