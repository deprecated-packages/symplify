<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Tests\Filters;

use Iterator;
use Latte\Runtime\Filters;
use Nette\Localization\Translator;
use PHPUnit\Framework\TestCase;
use Symplify\LattePHPStanCompiler\Latte\Filters\FilterMatcher;
use Symplify\LattePHPStanCompiler\ValueObject\DynamicCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\FunctionCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\StaticCallReference;

final class FilterMatcherTest extends TestCase
{
    private FilterMatcher $filterMatcher;

    protected function setUp(): void
    {
        $this->filterMatcher = new FilterMatcher([
            'translate' => ['Nette\Localization\Translator', 'translate'],
        ]);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(
        string $filterName,
        StaticCallReference|DynamicCallReference|FunctionCallReference|null $expectedCallReference
    ): void {
        $callReference = $this->filterMatcher->match($filterName);

        if ($expectedCallReference instanceof StaticCallReference) {
            $this->assertInstanceOf(StaticCallReference::class, $callReference);
            $this->assertEquals($callReference, $expectedCallReference);
        } elseif ($expectedCallReference instanceof DynamicCallReference) {
            $this->assertInstanceOf(DynamicCallReference::class, $callReference);
            $this->assertEquals($callReference, $expectedCallReference);
        } elseif ($expectedCallReference instanceof FunctionCallReference) {
            $this->assertInstanceOf(FunctionCallReference::class, $callReference);
            $this->assertEquals($callReference, $expectedCallReference);
        } else {
            $this->assertNull($callReference);
        }
    }

    public function provideData(): Iterator
    {
        yield ['date', new StaticCallReference(Filters::class, 'date')];

        yield ['datez', null];

        yield ['number', new FunctionCallReference('number_format')];

        yield ['translate', new DynamicCallReference(Translator::class, 'translate')];
    }
}
