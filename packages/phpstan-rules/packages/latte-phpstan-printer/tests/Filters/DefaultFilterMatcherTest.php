<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Tests\Filters;

use Iterator;
use Latte\Runtime\Filters;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Filters\DefaultFilterMatcher;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\StaticCallReference;

final class DefaultFilterMatcherTest extends TestCase
{
    private DefaultFilterMatcher $defaultFilterMatcher;

    protected function setUp(): void
    {
        $this->defaultFilterMatcher = new DefaultFilterMatcher();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filterName, StaticCallReference|null $expectedStaticCallReference): void
    {
        $staticCallReference = $this->defaultFilterMatcher->match($filterName);

        if ($expectedStaticCallReference instanceof StaticCallReference) {
            $this->assertInstanceOf(StaticCallReference::class, $staticCallReference);

            $this->assertEquals($staticCallReference, $expectedStaticCallReference);
        } else {
            $this->assertNull($staticCallReference);
        }
    }

    public function provideData(): Iterator
    {
        yield ['date', new StaticCallReference(Filters::class, 'date')];

        yield ['datez', null];
    }
}
