<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use function Safe\krsort;

final class LinkAppender
{
    /**
     * @var string[]
     */
    private $linksToAppend = [];

    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    public function __construct(LinksAnalyzer $linksAnalyzer)
    {
        $this->linksAnalyzer = $linksAnalyzer;
    }

    public function hasId(string $id): bool
    {
        return array_key_exists($id, $this->linksToAppend);
    }

    public function add(string $id, string $link): void
    {
        $this->linksToAppend[$id] = $link;
    }

    /**
     * @return string[]
     */
    public function getLinksToAppend(): array
    {
        krsort($this->linksToAppend);

        // filter out already existing links
        $this->removeAlreadyExistingLinks();

        return $this->linksToAppend;
    }

    private function removeAlreadyExistingLinks(): void
    {
        foreach (array_keys($this->linksToAppend) as $id) {
            if ($this->linksAnalyzer->hasLinkedId((string) $id)) {
                unset($this->linksToAppend[$id]);
            }
        }
    }
}
