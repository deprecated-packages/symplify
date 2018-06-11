<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

final class ChangeTree
{
    /**
     * @var Change[]
     */
    private $changes = [];

    /**
     * @var ChangeFactory
     */
    private $changeFactory;

    /**
     * @var Change[][]
     */
    private $changesInCategories = [];

    /**
     * @var Change[]
     */
    private $changesWithoutCategories = [];

    public function __construct(ChangeFactory $changeFactory)
    {
        $this->changeFactory = $changeFactory;
    }

    public function addPullRequestMessage(string $message): void
    {
        $this->changes[] = $this->changeFactory->createFromMessage($message);
    }

    /**
     * @return Change[]
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * @return Change[][]
     */
    public function getInCategories(): array
    {
        $this->filterChangesByCategory();

        return $this->changesInCategories;
    }

    /**
     * @return Change[]
     */
    public function getChangesWithoutCategory(): array
    {
        return $this->changesWithoutCategories;
    }

    private function filterChangesByCategory(): void
    {
        $this->changesInCategories = [];

        // filter
        foreach ($this->changes as $change) {
            if ($change->getCategory()) {
                $this->changesInCategories[$change->getCategory()][] = $change;
            } else {
                $this->changesWithoutCategories[] = $change;
            }
        }
    }
}
