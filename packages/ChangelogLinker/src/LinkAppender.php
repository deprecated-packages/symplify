<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

final class LinkAppender
{
    /**
     * @var string[]
     */
    private $linksToAppend = [];

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
        rsort($this->linksToAppend);

        return $this->linksToAppend;
    }
}
