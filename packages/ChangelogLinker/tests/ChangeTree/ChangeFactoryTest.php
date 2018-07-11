<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class ChangeFactoryTest extends TestCase
{
    /**
     * @var ChangeFactory
     */
    private $changeFactory;

    protected function setUp(): void
    {
        $this->changeFactory = new ChangeFactory(new GitCommitDateTagResolver(), ['A' => 'Aliased'], ['ego']);
    }

    /**
     * @dataProvider provideData()
     */
    public function testCategoriesAndPackages(string $message, string $expectedCategory, string $expectedPackage): void
    {
        $pullRequest = [
            'number' => null,
            'title' => 'Add cool feature',
            'merge_commit_sha' => 'random',
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
            'merge_commit_sha' => 'random',
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);
        $this->assertSame('- [#10] Add cool feature, Thanks to @me', $change->getMessage());

        $pullRequest = [
            'number' => 10,
            'title' => 'Add cool feature',
            'user' => [
                'login' => 'ego',
            ],
            'merge_commit_sha' => 'random',
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);
        $this->assertSame('- [#10] Add cool feature', $change->getMessage());
    }

    public function testGetMessageWithoutPackage(): void
    {
        $pullRequest = [
            'number' => 10,
            'title' => '[SomePackage] SomeMessage',
            'merge_commit_sha' => 'random',
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);

        $this->assertSame('- [#10] [SomePackage] SomeMessage', $change->getMessage());
        $this->assertSame('- [#10] SomeMessage', $change->getMessageWithoutPackage());
    }

    public function testTagDetection(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('This can be tested only with merge commit in monorepo, not split.');
        }

        $pullRequest = [
            'number' => 10,
            'title' => '[SomePackage] SomeMessage',
            'merge_commit_sha' => '58f3eea3a043998e272e70079bccb46fac10e4ad',
        ];
        $change = $this->changeFactory->createFromPullRequest($pullRequest);

        $this->assertSame('v4.2.0', $change->getTag());
    }
}
