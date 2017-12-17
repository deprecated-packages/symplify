<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class DiffLinksToVersionsWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $linkedVersionIds = [];

    /**
     * @var string[]
     */
    private $versionIds = [];

    public function processContent(string $content, string $repositoryLink): string
    {
        $this->collectLinkedVersionIds($content);
        $this->collectVersionsIds($content);

        $linksToAppend = [];
        foreach ($this->versionIds as $index => $versionId) {
            if ($this->shouldSkip($versionId, $index)) {
                continue;
            }

            $linksToAppend[] = sprintf(
                '[%s]: %s/compare/%s...%s',
                $versionId,
                $repositoryLink,
                $this->versionIds[$index + 1],
                $versionId
            );
        }

        if (! count($linksToAppend)) {
            return $content;
        }

        rsort($linksToAppend);

        // append new links to the file
        return $content . PHP_EOL . implode(PHP_EOL, $linksToAppend);
    }

    private function collectLinkedVersionIds(string $content): void
    {
        $matches = Strings::matchAll($content, '#\[(?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)\]: #');
        foreach ($matches as $match) {
            $this->linkedVersionIds[] = $match['versionId'];
        }
    }

    private function collectVersionsIds(string $content): void
    {
        $matches = Strings::matchAll($content, '#\#\# \[(?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)\]#');
        foreach ($matches as $match) {
            $this->versionIds[] = $match['versionId'];
        }
    }

    private function shouldSkip(string $versionId, int $index): bool
    {
        if (in_array($versionId, $this->linkedVersionIds, true)) {
            return true;
        }

        // last version, no previous one
        if (! isset($this->versionIds[$index + 1])) {
            return true;
        }

        return false;
    }
}
