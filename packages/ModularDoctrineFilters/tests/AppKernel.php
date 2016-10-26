<?php

declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ModularDoctrineFilters\SymplifyModularDoctrineFiltersBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct(rand(1, 100), true);
    }

    public function registerBundles() : array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SymplifyModularDoctrineFiltersBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
