<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;

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
     * @var bool
     */
    private $areFiltersEnabled = false;

    /**
     * @var ReflectionProperty
     */
    private $enabledFiltersPropertyReflection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addFilter(string $name, FilterInterface $filter) : void
    {
        $this->filters[$name] = $filter;
    }

    public function enableFilters() : void
    {
        if ($this->areFiltersEnabled) {
            return;
        }

        foreach ($this->filters as $name => $filter) {
            $this->addFilterToEnabledInFilterCollection($name, $filter);
        }

        $this->areFiltersEnabled = true;
    }

    private function addFilterToEnabledInFilterCollection(string $name, FilterInterface $filter) : void
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
