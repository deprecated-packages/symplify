<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoContainerInjectionInConstructorRule\Fixture;

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SkipContainerBuilder
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    public function getContainerBuilder(): ContainerBuilder
    {
        return $this->containerBuilder;
    }
}
