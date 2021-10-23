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
    /** @var array<string, string> */
    private array $staticFilters;

    /** @var array<string, string> */
    private array $nonStaticFilters;

    /** @var array<string, string> */
    private array $functionFilters;

    private Defaults $filtersDefaults;

    /**
     * @param array<string, string> $staticFilters
     * @param array<string, string> $nonStaticFilters
     * @param array<string, string> $functionFilters
     */
    public function __construct(array $staticFilters, array $nonStaticFilters, array $functionFilters)
    {
        $this->staticFilters = array_change_key_case($staticFilters, CASE_LOWER);
        $this->nonStaticFilters = array_change_key_case($nonStaticFilters, CASE_LOWER);
        $this->functionFilters = array_change_key_case($functionFilters, CASE_LOWER);
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
    ): StaticCallReference|NonStaticCallReference|FunctionCallReference|null {
        if (isset($this->staticFilters[$filterName])) {
            [$className, $methodName] = explode('::', $this->staticFilters[$filterName], 2);
            return new StaticCallReference($className, $methodName);
        }

        if (isset($this->nonStaticFilters[$filterName])) {
            [$className, $methodName] = explode('::', $this->nonStaticFilters[$filterName], 2);
            return new NonStaticCallReference($className, $methodName);
        }

        if (isset($this->functionFilters[$filterName])) {
            return new FunctionCallReference($this->functionFilters[$filterName]);
        }
        return null;
    }
}
