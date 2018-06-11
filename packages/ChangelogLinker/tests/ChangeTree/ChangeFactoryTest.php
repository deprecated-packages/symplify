<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\Configuration\Configuration;

final class ChangeFactoryTest extends TestCase
{
    /**
     * @var ChangeFactory
     */
    private $changeFactory;

    protected function setUp(): void
    {
        $this->changeFactory = new ChangeFactory(new Configuration(
            [],
            '',
            '',
            [],
            ['A' => 'Aliased']
        ));
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $message, string $expectedCategory, string $expectedPackage): void
    {
        $change = $this->changeFactory->createFromMessage($message);

        $this->assertSame($expectedCategory, $change->getCategory());
        $this->assertSame($expectedPackage, $change->getPackage());
    }

    public function provideData(): Iterator
    {
        yield ['Some message', 'Unknown Category', 'Unknown Package'];
        yield ['[A] Some message', 'Unknown Category', 'Aliased'];

        yield ['[CodingStandard] Add feature', 'Added', 'CodingStandard'];

        yield ['This fixed some bug', 'Fixed', 'Unknown Package'];
        yield ['Improve behavior', 'Changed', 'Unknown Package'];
        yield ['Remove this', 'Removed', 'Unknown Package'];
        yield ['All was deleted', 'Removed', 'Unknown Package'];
    }
}
