<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Git;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

/**
 * @requires PHP < 7.4
 */
final class GitCommitDateTagResolverTest extends TestCase
{
    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    protected function setUp(): void
    {
        $this->gitCommitDateTagResolver = new GitCommitDateTagResolver();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $commitHash, string $expectedTag): void
    {
        $this->assertSame($expectedTag, $this->gitCommitDateTagResolver->resolveCommitToTag($commitHash));
    }

    public function provideData(): Iterator
    {
        // different commit hashes after split
        if (defined('SYMPLIFY_MONOREPO')) {
            yield ['ef5e708', 'v4.1.1'];
            yield ['940ec99', 'v3.2.26'];
        } else {
            yield ['314fbcd', 'v4.2.1'];
            yield ['b9d71ff', 'v4.1.0'];
        }

        yield ['too-new', 'Unreleased'];
    }

    /**
     * @dataProvider provideDataResolveDateForTag()
     */
    public function testResolveDateForTag(string $tag, ?string $expectedTag): void
    {
        $this->assertSame($expectedTag, $this->gitCommitDateTagResolver->resolveDateForTag($tag));
    }

    public function provideDataResolveDateForTag(): Iterator
    {
        yield ['v4.4.0', '2018-06-03'];

        // different dates on tags after split
        if (defined('SYMPLIFY_MONOREPO')) {
            yield ['v4.4.1', '2018-06-07'];
            yield ['v4.4.2', '2018-06-10'];
        } else {
            yield ['v4.4.1', '2018-06-03'];
            yield ['v4.4.2', '2018-06-09'];
        }

        yield ['Unreleased', null];
    }
}
