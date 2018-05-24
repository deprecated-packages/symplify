<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Symplify\ChangelogLinker\Analyzer\LinkedVersionsAnalyzer;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;

final class DiffLinksToVersionsWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $versions = [];

    /**
     * @var string
     */
    private $repositoryUrl;

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

    public function __construct(string $repositoryUrl, LinkedVersionsAnalyzer $linkedVersionsAnalyzer, LinkAppender $linkAppender, VersionsAnalyzer $versionsAnalyzer)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->linkedVersionsAnalyzer = $linkedVersionsAnalyzer;
        $this->linkAppender = $linkAppender;
        $this->versionsAnalyzer = $versionsAnalyzer;
    }

    public function processContent(string $content): string
    {
        foreach ($this->versionsAnalyzer->getVersions() as $index => $version) {
            if ($this->shouldSkip($version, $index)) {
                continue;
            }

            $link = sprintf(
                '[%s]: %s/compare/%s...%s',
                $version,
                $this->repositoryUrl,
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

    private function shouldSkip(string $version, int $index): bool
    {
        if ($this->linkedVersionsAnalyzer->hasLinkedVersion($version)) {
            return true;
        }

        return ! $this->versionsAnalyzer->isLastVersion($version);
    }
}
