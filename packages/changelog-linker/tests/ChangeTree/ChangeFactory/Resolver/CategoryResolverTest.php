<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\Resolver;

use Iterator;
use Symplify\ChangelogLinker\Configuration\Category;
use Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\AbstractChangeFactoryTest;

final class CategoryResolverTest extends AbstractChangeFactoryTest
{
    /**
     * @dataProvider provideDataAdded()
     */
    public function testAdded(string $title): void
    {
        $change = $this->createChangeForTitle($title);
        $this->assertSame(Category::ADDED, $change->getCategory());
    }

    public function provideDataAdded(): Iterator
    {
        yield ['[CodingStandard] Add feature'];
        yield ['add'];
        yield ['adds'];
        yield ['added'];
        yield ['adding'];
    }

    /**
     * @dataProvider provideDataChanged()
     */
    public function testChanged(string $title): void
    {
        $change = $this->createChangeForTitle($title);
        $this->assertSame(Category::CHANGED, $change->getCategory());
    }

    public function provideDataChanged(): Iterator
    {
        yield ['Improve behavior'];
        yield ['change'];
        yield ['changes'];
        yield ['changed'];
        yield ['changing'];
        yield ['improve'];
        yield ['improves'];
        yield ['improved'];
        yield ['improving'];
        yield ['bump'];
        yield ['bumps'];
        yield ['bumped'];
        yield ['bumping'];
        yield ['allow'];
        yield ['allows'];
        yield ['allowed'];
        yield ['allowing'];
        yield ['disallow'];
        yield ['disallows'];
        yield ['disallowed'];
        yield ['disallowing'];
        yield ['return'];
        yield ['returns'];
        yield ['returned'];
        yield ['returning'];
        yield ['rename'];
        yield ['renames'];
        yield ['renamed'];
        yield ['renaming'];
        yield ['decouple'];
        yield ['decouples'];
        yield ['decoupled'];
        yield ['decoupling'];
        yield ['now'];
    }

    /**
     * @dataProvider provideDataFixed()
     */
    public function testFixed(string $title): void
    {
        $change = $this->createChangeForTitle($title);
        $this->assertSame(Category::FIXED, $change->getCategory());
    }

    public function provideDataFixed(): Iterator
    {
        yield ['This fixed some bug'];
        yield ['fix'];
        yield ['fixes'];
        yield ['fixed'];
        yield ['fixing'];
    }

    /**
     * @dataProvider provideDataRemoved()
     */
    public function testRemoved(string $title): void
    {
        $change = $this->createChangeForTitle($title);
        $this->assertSame(Category::REMOVED, $change->getCategory());
    }

    public function provideDataRemoved(): Iterator
    {
        yield ['Remove this'];
        yield ['All was deleted'];
        yield ['Removing all classes ending "Adapter" for no reason'];
        yield ['[Skeleton] Deletes unnecessary templates'];
        yield ['remove'];
        yield ['removes'];
        yield ['removed'];
        yield ['removing'];
        yield ['delete'];
        yield ['deletes'];
        yield ['deleted'];
        yield ['deleting'];
        yield ['drop'];
        yield ['drops'];
        yield ['dropped'];
        yield ['dropping'];
    }

    /**
     * @dataProvider provideDataUnknownCategory()
     */
    public function testChangedCategoryFallback(string $title): void
    {
        $change = $this->createChangeForTitle($title);
        $this->assertSame(Category::CHANGED, $change->getCategory(), $title);
    }

    public function provideDataUnknownCategory(): Iterator
    {
        yield ['New design of a hydroplane'];
        yield ['changelog'];
        yield ['exchanged'];
        yield ['addidas'];
        yield ['sexchanging'];
        yield ['improvesation'];
        yield ['disimproves'];
        yield ['bumpage'];
        yield ['doubledrop'];
        yield ['unremove'];
        yield ['unreturned'];
    }
}
