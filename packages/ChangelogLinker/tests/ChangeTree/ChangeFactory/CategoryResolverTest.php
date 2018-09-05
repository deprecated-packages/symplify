<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory;

use Iterator;
use Symplify\ChangelogLinker\Configuration\Category;

final class CategoryResolverTest extends AbstractChangeFactoryTest
{
    /**
     * @dataProvider provideDataAdded()
     */
    public function testAdded(string $title): void
    {
        $this->pullRequest['title'] = $title;
        $change = $this->changeFactory->createFromPullRequest($this->pullRequest);
        $this->assertSame(Category::ADDED, $change->getCategory());
    }

    /**
     * @dataProvider provideDataChanged()
     */
    public function testChanged(string $title): void
    {
        $this->pullRequest['title'] = $title;
        $change = $this->changeFactory->createFromPullRequest($this->pullRequest);
        $this->assertSame(Category::CHANGED, $change->getCategory());
    }

    /**
     * @dataProvider provideDataFixed()
     */
    public function testFixed(string $title): void
    {
        $this->pullRequest['title'] = $title;
        $change = $this->changeFactory->createFromPullRequest($this->pullRequest);
        $this->assertSame(Category::FIXED, $change->getCategory());
    }

    /**
     * @dataProvider provideDataRemoved()
     */
    public function testRemoved(string $title): void
    {
        $this->pullRequest['title'] = $title;
        $change = $this->changeFactory->createFromPullRequest($this->pullRequest);
        $this->assertSame(Category::REMOVED, $change->getCategory());
    }

    /**
     * @dataProvider provideDataRemoved()
     */
    public function testUnknonw(string $title): void
    {
        $this->pullRequest['title'] = $title;
        $change = $this->changeFactory->createFromPullRequest($this->pullRequest);
        $this->assertSame('Unknown Category', $change->getCategory());
    }

    public function provideDataUnknownCategory(): Iterator
    {
        yield ['New design of a hydroplane', 'Unknown Category'];
    }

    public function provideDataAdded(): Iterator
    {
        yield ['[CodingStandard] Add feature'];
        yield from $this->provideDataForCategoryKeywords(['add', 'adds', 'added', 'adding']);
    }

    public function provideDataChanged(): Iterator
    {
        yield ['Improve behavior'];
        yield from $this->provideDataForCategoryKeywords(['change', 'changes', 'changed', 'changing']);
        yield from $this->provideDataForCategoryKeywords(['improve', 'improves', 'improved', 'improving']);
        yield from $this->provideDataForCategoryKeywords(['bump', 'bumps', 'bumped', 'bumping']);
        yield from $this->provideDataForCategoryKeywords(['allow', 'allows', 'allowed', 'allowing']);
        yield from $this->provideDataForCategoryKeywords(['disallow', 'disallows', 'disallowed', 'disallowing']);
        yield from $this->provideDataForCategoryKeywords(['return', 'returns', 'returned', 'returning']);
        yield from $this->provideDataForCategoryKeywords(['rename', 'renames', 'renamed', 'renaming']);
        yield from $this->provideDataForCategoryKeywords(['decouple', 'decouples', 'decoupled', 'decoupling']);
        yield from $this->provideDataForCategoryKeywords(['now']);
    }

    public function provideDataFixed(): Iterator
    {
        yield ['This fixed some bug'];
        yield from $this->provideDataForCategoryKeywords(['fix', 'fixes', 'fixed', 'fixing']);
    }

    public function provideDataRemoved(): Iterator
    {
        yield ['Remove this'];
        yield ['All was deleted'];
        yield ['Removing all classes ending "Adapter" for no reason'];
        yield ['[Skeleton] Deletes unnecessary templates'];
        yield from $this->provideDataForCategoryKeywords(['remove', 'removes', 'removed', 'removing']);
        yield from $this->provideDataForCategoryKeywords(['delete', 'deletes', 'deleted', 'deleting']);
        yield from $this->provideDataForCategoryKeywords(['drop', 'drops', 'dropped', 'dropping']);
    }

    /**
     * @param string[] $keywords
     */
    private function provideDataForCategoryKeywords(array $keywords): Iterator
    {
        foreach ($keywords as $keyword) {
            yield [$keyword, $expectedCategory];
            yield ['prefix' . $keyword, 'Unknown Category'];
            yield [$keyword . 'postfix', 'Unknown Category'];
        }
    }
}
