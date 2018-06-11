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
}
