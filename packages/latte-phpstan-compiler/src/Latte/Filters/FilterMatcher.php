<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Latte\Filters;

use Latte\Runtime\Defaults;
use Symplify\LattePHPStanCompiler\ValueObject\FunctionCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\NonStaticCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\StaticCallReference;

/**
 * @see \Symplify\LattePHPStanCompiler\Tests\Filters\FilterMatcherTest
 */
final class FilterMatcher
{
    private array $filters;

    private Defaults $filtersDefaults;

    /**
     * @param array<string, string|array{callback: string, static: bool}> $filters
     */
    public function __construct(array $filters) {
        $this->filters = array_change_key_case($filters, CASE_LOWER);
        $this->filtersDefaults = new Defaults();
    }

    /**
     * @return StaticCallReference|NonStaticCallReference|FunctionCallReference|null
     */
    public function match(string $filterName)
    {
        $callReference = $this->findInDefaultFilters($filterName);
        if ($callReference !== null) {
            return $callReference;
        }
        return $this->findInConfiguredFilters($filterName);
    }

    private function findInDefaultFilters(string $filterName): StaticCallReference|FunctionCallReference|null
    {
        // match filter name in
        $filterCallable = $this->filtersDefaults->getFilters()[$filterName] ?? null;
        if (is_string($filterCallable)) {
            return new FunctionCallReference($filterCallable);
        }

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

    private function findInConfiguredFilters(
        string $filterName
    ): StaticCallReference|NonStaticCallReference|FunctionCallReference|null
    {
        if (! isset($this->filters[$filterName])) {
            return null;
        }

        $filter = $this->filters[$filterName];
        if (is_string($filter)) {
            return new FunctionCallReference($filter);
        }

        [$className, $methodName] = explode('::', $filter['callback'], 2);

        if ($filter['static']) {
            return new StaticCallReference($className, $methodName);
        }
        return new NonStaticCallReference($className, $methodName);
    }
}
