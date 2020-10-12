<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractKernelTestCase
{
    /**
     * @var KernelInterface
     */
    protected static $kernel;

    /**
     * @var ContainerInterface|Container
     */
    protected static $container;
}