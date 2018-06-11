<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

final class ChangeTree
{
    /**
     * @var string[]
     */
    private $changes = [];

    public function addChange(string $change): void
    {
        $this->changes[] = $change;
    }

    /**
     * @return string[]
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
}
