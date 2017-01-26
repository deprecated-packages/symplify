<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Laravel;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Nette\Utils\Strings;
use ReflectionFunction;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\FilterManager;

/**
 * @property \Illuminate\Foundation\Application $app
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
                /** @var FilterInterface $filterService */
                $filterService = $this->app->make($name);
                $filterManager->addFilter($name, $filterService);
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

        if (isset($staticVariables['abstract'])) {
            if (is_a($staticVariables['abstract'], $classOrInterfaceType, true)) {
                return true;
            }
        }

        if (isset($staticVariables['concrete'])) {
            if (is_a($staticVariables['concrete'], $classOrInterfaceType, true)) {
                return true;
            }
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
            ! Str::startsWith($closureReflection->name, 'Illuminate\\Container');
    }
}
