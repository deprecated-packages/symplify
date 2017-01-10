<?php

declare(strict_types = 1);

namespace Zenify\ModularLatteFilters\DI;

use Latte\Engine;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Zenify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;
use Zenify\ModularLatteFilters\Exception\DI\MissingLatteDefinitionException;

final class ModularLatteFiltersExtension extends CompilerExtension
{

    /**
     * @var string
     */
    const APPLICATION_LATTE_FACTORY_INTERFACE = ILatteFactory::class;


    public function beforeCompile()
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $latteDefinition = $this->getLatteDefinition();
        $latteFiltersDefinitions = $containerBuilder->findByType(LatteFiltersProviderInterface::class);
        foreach ($latteFiltersDefinitions as $latteFilterProviderDefinition) {
            $latteDefinition->addSetup(
                'foreach (?->getFilters() as $name => $callback) {
					?->addFilter($name, $callback);
				}',
                ['@' . $latteFilterProviderDefinition->getClass(), '@self']
            );
        }
    }


    private function getLatteDefinition() : ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();

        if ($containerBuilder->getByType(Engine::class)) {
            $serviceName = $containerBuilder->getByType(Engine::class);
        } elseif ($containerBuilder->getByType(self::APPLICATION_LATTE_FACTORY_INTERFACE)) {
            $serviceName = $containerBuilder->getByType(self::APPLICATION_LATTE_FACTORY_INTERFACE);
        } else {
            throw new MissingLatteDefinitionException(
                sprintf(
                    'No services providing Latte\Engine was found. Register service either of %s or %s type.',
                    Engine::class,
                    ILatteFactory::class
                )
            );
        }

        return $containerBuilder->getDefinition($serviceName);
    }
}
