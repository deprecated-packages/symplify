<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory;

use Iterator;

final class ChangeFactoryTest extends AbstractChangeFactoryTest
{
    public function testeEgoTag(): void
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

    /**
     * @dataProvider provideDataForMessageWithoutPackage()
     */
    public function testGetMessageWithoutPackage(
        string $title,
        string $expectedMessage,
        string $expectedMessageWithoutPackage
    ): void {
        $pullRequest = [
            'number' => 10,
            'title' => $title,
            'merge_commit_sha' => 'random',
        ];

        $change = $this->changeFactory->createFromPullRequest($pullRequest);

        $this->assertSame($expectedMessage, $change->getMessage());
        $this->assertSame($expectedMessageWithoutPackage, $change->getMessageWithoutPackage());
    }

    public function provideDataForMessageWithoutPackage(): Iterator
    {
        yield ['[SomePackage] SomeMessage', '- [#10] [SomePackage] SomeMessage', '- [#10] SomeMessage'];
        yield ['[aliased-package] SomeMessage', '- [#10] [aliased-package] SomeMessage', '- [#10] SomeMessage'];
        yield ['[coding-standards] SomeMessage', '- [#10] [coding-standards] SomeMessage', '- [#10] SomeMessage'];
        yield ['[shopsys\framework] SomeMessage', '- [#10] [shopsys\framework] SomeMessage', '- [#10] SomeMessage'];
        yield ['[framework] javascript stuff', '- [#10] [framework] javascript stuff', '- [#10] javascript stuff'];
        yield ['*SomeMessage', '- [#10] \*SomeMessage', '- [#10] \*SomeMessage'];
        yield ['*SomeMessage**', '- [#10] \*SomeMessage\*\*', '- [#10] \*SomeMessage\*\*'];
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
}
