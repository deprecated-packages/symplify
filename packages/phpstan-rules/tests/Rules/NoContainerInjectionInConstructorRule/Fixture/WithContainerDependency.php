<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoContainerInjectionInConstructorRule\Fixture;

use Psr\Container\ContainerInterface;

final class WithContainerDependency
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
