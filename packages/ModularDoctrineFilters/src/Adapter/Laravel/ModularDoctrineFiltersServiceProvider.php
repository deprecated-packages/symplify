<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Laravel;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionFunction;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\FilterManager;

/**
 * @property Application $app
 */
final class ModularDoctrineFiltersServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FilterManagerInterface::class, function (Application $application) {
            return new FilterManager($application->make(EntityManagerInterface::class));
        });

        $this->app->alias(FilterManagerInterface::class, FilterManager::class);
    }

    public function boot(FilterManagerInterface $filterManager)
    {
        foreach ($this->app->getBindings() as $name => $definition) {
            // todo: skip later...
            // todo: detect if implements Interface before creating?
            // use some class map like in Symfony?
            // https://laravel.com/docs/5.4/container#container-events

            if ($this->isDefinitionOfType($definition, FilterInterface::class)) {
                $filterManager->addFilter($name, $this->app->make($name));
            }
        }
    }

    private function isDefinitionOfType(array $definition, string $classOrInterfaceType) : bool
    {
        $closureReflection = new ReflectionFunction($definition['concrete']);

        if ($this->isLaravelSystem($closureReflection)) {
            return false;
        }

        $staticVariables = $closureReflection->getStaticVariables();

        if ($this->hasVariableOfNameAndType($staticVariables, 'abstract', $classOrInterfaceType)) {
            return true;
        }

        if ($this->hasVariableOfNameAndType($staticVariables, 'variable', $classOrInterfaceType)) {
            return true;
        }

        // closure explicit return type
        if ((string) $closureReflection->getReturnType() === $classOrInterfaceType) {
            return true;
        }

        return false;
    }

    private function isLaravelSystem(ReflectionFunction $closureReflection) : bool
    {
        return Str::startsWith($closureReflection->name, 'Illuminate') &&
            Str::startsWith($closureReflection->name, 'Illuminate\\Container') === false;
    }

    private function hasVariableOfNameAndType(array $staticVariables, string $name, string $classOrInterfaceType) : bool
    {
        if (! isset($staticVariables[$name])) {
            return false;
        }

        return is_a($staticVariables[$name], $classOrInterfaceType, true);
    }
}
