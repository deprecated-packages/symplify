<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\ChangeTree\ChangeTree;
use Symplify\ChangelogLinker\Configuration\Configuration;

final class ChangeTreeTest extends TestCase
{
    /**
     * @var ChangeTree
     */
    private $changeTree;

    protected function setUp(): void
    {
        $changeFactory = new ChangeFactory(new Configuration(
            [],
            '',
            '',
            [],
            []
        ));

        $this->changeTree = new ChangeTree($changeFactory);
    }

    public function test(): void
    {
        $this->changeTree->addPullRequestMessage('Some message');
        $this->assertCount(1, $this->changeTree->getChanges());
    }
}
