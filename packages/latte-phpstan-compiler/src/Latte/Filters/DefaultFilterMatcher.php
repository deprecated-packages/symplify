<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Latte\Filters;

use Latte\Runtime\Defaults;
use Symplify\LattePHPStanCompiler\ValueObject\StaticCallReference;

/**
 * @see \Symplify\LattePHPStanCompiler\Tests\Filters\DefaultFilterMatcherTest
 */
final class DefaultFilterMatcher
{
    private Defaults $filtersDefaults;

    public function __construct()
    {
        $this->filtersDefaults = new Defaults();
    }

    public function match(string $filterName): ?StaticCallReference
    {
        // match filter name in
        $filterCallable = $this->filtersDefaults->getFilters()[$filterName] ?? null;
        if (! is_array($filterCallable)) {
            return null;
        }

        /** @var mixed[] $filterCallable */
        if (count($filterCallable) !== 2) {
            return null;
        }

        $filterClass = $filterCallable[0];
        $filterMethod = $filterCallable[1];

        return new StaticCallReference($filterClass, $filterMethod);
    }
}
