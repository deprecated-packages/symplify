<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\Resolver;

use Iterator;
use Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\AbstractChangeFactoryTest;
use Symplify\ChangelogLinker\ValueObject\Category;

final class CategoryResolverTest extends AbstractChangeFactoryTest
{
    /**
     * @dataProvider provideDataAdded()
     * @dataProvider provideDataChanged()
     * @dataProvider provideDataFixed()
     * @dataProvider provideDataUnknownCategory()
     */
    public function test(string $title, string $expectedCategory): void
    {
        $change = $this->createChangeForTitle($title);
        $this->assertSame($expectedCategory, $change->getCategory());
    }

    public function provideDataAdded(): Iterator
    {
        yield ['[CodingStandard] Add feature', Category::ADDED];
        yield ['add', Category::ADDED];
        yield ['adds', Category::ADDED];
        yield ['added', Category::ADDED];
        yield ['adding', Category::ADDED];
    }

    public function provideDataChanged(): Iterator
    {
        yield ['Improve behavior', Category::CHANGED];
        yield ['change', Category::CHANGED];
        yield ['changes', Category::CHANGED];
        yield ['changed', Category::CHANGED];
        yield ['changing', Category::CHANGED];
        yield ['improve', Category::CHANGED];
        yield ['improves', Category::CHANGED];
        yield ['improved', Category::CHANGED];
        yield ['improving', Category::CHANGED];
        yield ['bump', Category::CHANGED];
        yield ['bumps', Category::CHANGED];
        yield ['bumped', Category::CHANGED];
        yield ['bumping', Category::CHANGED];
        yield ['allow', Category::CHANGED];
        yield ['allows', Category::CHANGED];
        yield ['allowed', Category::CHANGED];
        yield ['allowing', Category::CHANGED];
        yield ['disallow', Category::CHANGED];
        yield ['disallows', Category::CHANGED];
        yield ['disallowed', Category::CHANGED];
        yield ['disallowing', Category::CHANGED];
        yield ['return', Category::CHANGED];
        yield ['returns', Category::CHANGED];
        yield ['returned', Category::CHANGED];
        yield ['returning', Category::CHANGED];
        yield ['rename', Category::CHANGED];
        yield ['renames', Category::CHANGED];
        yield ['renamed', Category::CHANGED];
        yield ['renaming', Category::CHANGED];
        yield ['decouple', Category::CHANGED];
        yield ['decouples', Category::CHANGED];
        yield ['decoupled', Category::CHANGED];
        yield ['decoupling', Category::CHANGED];
        yield ['now', Category::CHANGED];
    }

    public function provideDataFixed(): Iterator
    {
        yield ['This fixed some bug', Category::FIXED];
        yield ['fix', Category::FIXED];
        yield ['fixes', Category::FIXED];
        yield ['fixed', Category::FIXED];
        yield ['fixing', Category::FIXED];
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

    public function provideDataUnknownCategory(): Iterator
    {
        yield ['New design of a hydroplane', Category::CHANGED];
        yield ['changelog', Category::CHANGED];
        yield ['exchanged', Category::CHANGED];
        yield ['addidas', Category::CHANGED];
        yield ['sexchanging', Category::CHANGED];
        yield ['improvesation', Category::CHANGED];
        yield ['disimproves', Category::CHANGED];
        yield ['bumpage', Category::CHANGED];
        yield ['doubledrop', Category::CHANGED];
        yield ['unremove', Category::CHANGED];
        yield ['unreturned', Category::CHANGED];
    }
}
