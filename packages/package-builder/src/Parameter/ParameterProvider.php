<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Parameter;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see \Symplify\PackageBuilder\Tests\Parameter\ParameterProviderTest
 */
final class ParameterProvider
{
    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @param Container|ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->parameters = $container->getParameterBag()
            ->all();
    }

    /**
     * @return mixed|null
     */
    public function provideParameter(string $name)
    {
        return $this->parameters[$name] ?? null;
    }

    public function provideStringParameter(string $name): string
    {
        return $this->parameters[$name] ?? '';
    }

    /**
     * @return mixed[]
     */
    public function provideArrayParameter(string $name): array
    {
        return $this->parameters[$name] ?? [];
    }

    public function provideBoolParameter(string $parameterName): bool
    {
        return $this->parameters[$parameterName] ?? false;
    }

    public function changeParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @return mixed[]
     */
    public function provide(): array
    {
        return $this->parameters;
    }
}
