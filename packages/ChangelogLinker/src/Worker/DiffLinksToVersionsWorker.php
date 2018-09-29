<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use function Safe\sprintf;

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
     * @var string
     */
    private $repositoryUrl;

    public function __construct(
        LinkAppender $linkAppender,
        VersionsAnalyzer $versionsAnalyzer,
        LinksAnalyzer $linksAnalyzer,
        string $repositoryUrl
    ) {
        $this->linkAppender = $linkAppender;
        $this->versionsAnalyzer = $versionsAnalyzer;
        $this->linksAnalyzer = $linksAnalyzer;
        $this->repositoryUrl = $repositoryUrl;
    }

    public function processContent(string $content): string
    {
        // we need more than 1 version to make A...B
        if (count($this->versionsAnalyzer->getVersions()) <= 1) {
            return $content;
        }

        $versions = $this->versionsAnalyzer->getVersions();
        foreach ($versions as $index => $version) {
            if ($this->shouldSkip($versions, $index)) {
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

    /**
     * @param string[] $versions
     */
    private function shouldSkip(array $versions, int $index): bool
    {
        // there is no next version to compare this with
        if (! isset($versions[$index + 1])) {
            return true;
        }

        $version = $versions[$index];
        return $this->linksAnalyzer->hasLinkedId($version);
    }
}
