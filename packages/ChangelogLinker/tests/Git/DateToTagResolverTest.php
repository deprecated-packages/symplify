<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Git;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Git\DateToTagResolver;

/**
 * Note: uses data from symplfy/symplify repository, needs to be fully cloned
 */
final class DateToTagResolverTest extends TestCase
{
    /**
     * @var DateToTagResolver
     */
    private $dateToTagResolver;

    protected function setUp(): void
    {
        $this->dateToTagResolver = new DateToTagResolver();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $commitHash, string $expectedTag): void
    {
        $this->assertSame($expectedTag, $this->dateToTagResolver->resolveCommitToTag($commitHash));
    }

    public function provideData(): Iterator
    {
        yield ['ef5e708', 'v4.1.1'];
        yield ['940ec99', 'v3.2.26'];
        yield ['too-new', 'Unreleased'];
    }
}
