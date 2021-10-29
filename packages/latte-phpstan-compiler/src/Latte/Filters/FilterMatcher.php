<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Latte\Filters;

use Latte\Runtime\Defaults;
use ReflectionClass;
use ReflectionException;
use Symplify\LattePHPStanCompiler\ValueObject\DynamicCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\FunctionCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\StaticCallReference;

/**
 * @see \Symplify\LattePHPStanCompiler\Tests\Filters\FilterMatcherTest
 */
final class FilterMatcher
{
    /**
     * @var array<string, string|array{string, string}>
     */
    private array $latteFilters = [];

    private Defaults $filtersDefaults;

    /**
     * @param array<string, string|array{string, string}> $latteFilters
     */
    public function __construct(array $latteFilters)
    {
        $this->latteFilters = array_change_key_case($latteFilters, CASE_LOWER);
        $this->filtersDefaults = new Defaults();
    }

    /**
     * @return StaticCallReference|DynamicCallReference|FunctionCallReference|null
     */
    public function match(string $filterName)
    {
        $callReference = $this->findInDefaultFilters($filterName);
        if ($callReference !== null) {
            return $callReference;
        }

        return $this->findInConfiguredFilters($filterName);
    }

    private function findInDefaultFilters(
        string $filterName
    ): StaticCallReference|DynamicCallReference|FunctionCallReference|null {
        // match filter name in
        $filterCallable = $this->filtersDefaults->getFilters()[$filterName] ?? null;
        return $this->createCallReference($filterCallable);
    }

    private function findInConfiguredFilters(
        string $filterName
    ): StaticCallReference|DynamicCallReference|FunctionCallReference|null {
        $filterCallable = $this->latteFilters[$filterName] ?? null;
        return $this->createCallReference($filterCallable);
    }

    /**
     * @param mixed $filterCallable
     */
    private function createCallReference(
        $filterCallable
    ): StaticCallReference|DynamicCallReference|FunctionCallReference|null {
        if ($filterCallable === null) {
            return null;
        }

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

        try {
            $reflectionClass = new ReflectionClass($filterClass);
            $reflectionMethod = $reflectionClass->getMethod($filterMethod);
        } catch (ReflectionException) {
            return null;
        }

        if ($reflectionMethod->isStatic()) {
            return new StaticCallReference($filterClass, $filterMethod);
        }

        return new DynamicCallReference($filterClass, $filterMethod);
    }
}
