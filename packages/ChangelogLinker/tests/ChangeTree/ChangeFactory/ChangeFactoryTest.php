<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class ChangeFactoryTest extends TestCase
{
    /**
     * @var ChangeFactory|null
     */
    private static $cachedChangeFactory;

    /**
     * @var ChangeFactory
     */
    private $changeFactory;

    protected function setUp(): void
    {
        // this is needed, because every item in dataProviders resets $changeFactory property to null
        if (self::$cachedChangeFactory) {
            $this->changeFactory = self::$cachedChangeFactory;
        } else {
            $this->changeFactory = new ChangeFactory(new GitCommitDateTagResolver(), ['A' => 'Aliased'], ['ego']);
            self::$cachedChangeFactory = $this->changeFactory;
        }
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

        yield ['New design of a hydroplane', 'Unknown Category', 'Unknown Package'];
        yield ['Removing all classes ending "Adapter" for no reason', 'Removed', 'Unknown Package'];
        yield ['[Skeleton] Deletes unnecessary templates', 'Removed', 'Skeleton'];

        yield from $this->provideDataForCategoryKeywords(['add', 'adds', 'added', 'adding'], 'Added');

        yield from $this->provideDataForCategoryKeywords(['fix', 'fixes', 'fixed', 'fixing'], 'Fixed');

        yield from $this->provideDataForCategoryKeywords(['change', 'changes', 'changed', 'changing'], 'Changed');
        yield from $this->provideDataForCategoryKeywords(['improve', 'improves', 'improved', 'improving'], 'Changed');
        yield from $this->provideDataForCategoryKeywords(['bump', 'bumps', 'bumped', 'bumping'], 'Changed');
        yield from $this->provideDataForCategoryKeywords(['allow', 'allows', 'allowed', 'allowing'], 'Changed');
        yield from $this->provideDataForCategoryKeywords(
            ['disallow', 'disallows', 'disallowed', 'disallowing'],
            'Changed'
        );
        yield from $this->provideDataForCategoryKeywords(['return', 'returns', 'returned', 'returning'], 'Changed');
        yield from $this->provideDataForCategoryKeywords(['rename', 'renames', 'renamed', 'renaming'], 'Changed');
        yield from $this->provideDataForCategoryKeywords(
            ['decouple', 'decouples', 'decoupled', 'decoupling'],
            'Changed'
        );
        yield from $this->provideDataForCategoryKeywords(['now'], 'Changed');

        yield from $this->provideDataForCategoryKeywords(['remove', 'removes', 'removed', 'removing'], 'Removed');
        yield from $this->provideDataForCategoryKeywords(['delete', 'deletes', 'deleted', 'deleting'], 'Removed');
        yield from $this->provideDataForCategoryKeywords(['drop', 'drops', 'dropped', 'dropping'], 'Removed');
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
            $this->markTestSkipped('Can be tested only with merge commit in monorepo, not in split where are no PRs.');
        }

        $pullRequest = [
            'number' => 10,
            'title' => '[SomePackage] SomeMessage',
            'merge_commit_sha' => '58f3eea3a043998e272e70079bccb46fac10e4ad',
        ];
        $change = $this->changeFactory->createFromPullRequest($pullRequest);

        $this->assertSame('v4.2.0', $change->getTag());
    }

    /**
     * @param string[] $keywords
     */
    private function provideDataForCategoryKeywords(array $keywords, string $expectedCategory): Iterator
    {
        foreach ($keywords as $keyword) {
            yield [$keyword, $expectedCategory, 'Unknown Package'];
            yield ['prefix' . $keyword, 'Unknown Category', 'Unknown Package'];
            yield [$keyword . 'postfix', 'Unknown Category', 'Unknown Package'];
        }
    }
}
