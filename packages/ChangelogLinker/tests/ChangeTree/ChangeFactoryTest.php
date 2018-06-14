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
        $configuration = new Configuration(['ego'], '', '', [], ['A' => 'Aliased']);

        $this->changeFactory = new ChangeFactory($configuration);
    }

    /**
     * @dataProvider provideData()
     */
    public function testCategoriesAndPackages(string $message, string $expectedCategory, string $expectedPackage): void
    {
        $pullRequest = [
            'number' => null,
            'title' => 'Add cool feature',
        ];
        $pullRequest['title'] = $message;

        $change = $this->changeFactory->createFromPullRequest($pullRequest);

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

    public function testEgoTag(): void
    {
        $pullRequest = [
            'number' => 10,
            'title' => 'Add cool feature',
            'user' => [
                'login' => 'me',
            ],
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);
        $this->assertSame('- [#10] Add cool feature, Thanks to @me', $change->getMessage());

        $pullRequest = [
            'number' => 10,
            'title' => 'Add cool feature',
            'user' => [
                'login' => 'ego',
            ],
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);
        $this->assertSame('- [#10] Add cool feature', $change->getMessage());
    }

    public function testGetMessageWithoutPackage(): void
    {
        $pullRequest = [
            'number' => 10,
            'title' => '[SomePackage] SomeMessage',
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);

        $this->assertSame('- [#10] [SomePackage] SomeMessage', $change->getMessage());
        $this->assertSame('- [#10] SomeMessage', $change->getMessageWithoutPackage());
    }
}
