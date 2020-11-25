<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\CaseConverter;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\PHPStanPHPConfig\Reflection\ConstructorParameterNameResolver;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ServicesConverter
{
    /**
     * @var ConstructorParameterNameResolver
     */
    private $constructorParameterNameResolver;

    public function __construct(ConstructorParameterNameResolver $constructorParameterNameResolver)
    {
        $this->constructorParameterNameResolver = $constructorParameterNameResolver;
    }

    /**
     * @return mixed[]
     */
    public function convertContainerBuilder(ContainerBuilder $containerBuilder): array
    {
        $services = [];
        foreach ($containerBuilder->getDefinitions() as $serviceDefinition) {
            $serviceClass = $serviceDefinition->getClass();
            if ($serviceClass === null) {
                continue;
            }

            if (is_a($serviceClass, ContainerInterface::class, true)) {
                continue;
            }

            $service = [
                'class' => $serviceClass,
            ];

            /** @noRector */
            if (is_a($serviceClass, 'PHPStan\Rules\Rule', true)) {
                $service['tags'] = ['phpstan.rules.rule'];
            }

            $arguments = $this->convertServiceArguments($serviceDefinition, $serviceClass);
            if ($arguments !== []) {
                $service['arguments'] = $arguments;
            }

            $services[] = $service;
        }

        return $services;
    }

    /**
     * @return array<string, mixed>
     */
    private function convertServiceArguments(Definition $serviceDefinition, string $serviceClass): array
    {
        // get name from the position!
        $arguments = [];
        foreach ($serviceDefinition->getArguments() as $key => $value) {
            if (! is_int($key)) {
                throw new ShouldNotHappenException();
            }

            $argumentName = $this->constructorParameterNameResolver->resolveFromClassAndArgumentPosition(
                $serviceClass,
                $key
            );
            $arguments[$argumentName] = $value;
        }

        return $arguments;
    }
}
