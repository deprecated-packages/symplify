<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Git;

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

    public function test(): void
    {
        $this->assertSame('v2.5.9', $this->dateToTagResolver->resolveDateToTag('2017-10-26'));
        $this->assertSame('v2.5.11', $this->dateToTagResolver->resolveDateToTag('2018-01-01'));
        $this->assertSame('v3.2.9', $this->dateToTagResolver->resolveDateToTag('2018-01-23'));
        $this->assertSame('v4.2.0', $this->dateToTagResolver->resolveDateToTag('2018-05-05'));
        $this->assertSame('Unreleased', $this->dateToTagResolver->resolveDateToTag('2118-05-05'));
    }
}
