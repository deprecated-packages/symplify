<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Filter;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterManagerInterface;

final class FilterManager implements FilterManagerInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ReflectionProperty
     */
    private $enabledFiltersPropertyReflection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addFilter(string $name, FilterInterface $filter)
    {
        $this->filters[$name] = $filter;
    }

    public function enableFilters()
    {
        foreach ($this->filters as $name => $filter) {
            $this->addFilterToEnabledInFilterCollection($name, $filter);
        }
    }

    private function addFilterToEnabledInFilterCollection(string $name, FilterInterface $filter)
    {
        $enabledFiltersReflection = $this->getEnabledFiltersPropertyReflectionWithAccess();
        $filterCollection = $this->entityManager->getFilters();

        $enabledFilters = $enabledFiltersReflection->getValue($filterCollection);
        $enabledFilters[$name] = $filter;

        $enabledFiltersReflection->setValue($filterCollection, $enabledFilters);
    }

    private function getEnabledFiltersPropertyReflectionWithAccess() : ReflectionProperty
    {
        if ($this->enabledFiltersPropertyReflection) {
            return $this->enabledFiltersPropertyReflection;
        }

        $filterCollection = $this->entityManager->getFilters();

        $filterCollectionReflection = new ReflectionClass($filterCollection);
        $enabledFiltersReflection = $filterCollectionReflection->getProperty('enabledFilters');
        $enabledFiltersReflection->setAccessible(true);

        $this->enabledFiltersPropertyReflection = $enabledFiltersReflection;

        return $this->enabledFiltersPropertyReflection;
    }
}
