<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Tests\HttpKernel;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\DoctrineBehaviors\DoctrineBehaviorsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\SymfonyRouteUsage\SymfonyRouteUsageBundle;

final class SymfonyRouteUsageKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config_test.php');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [
            new SymfonyRouteUsageBundle(),
            new DoctrineBehaviorsBundle(),
            // tests
            new DAMADoctrineTestBundle(),
            // symfony app
            new DoctrineBundle(),
            new FrameworkBundle(),
            new SecurityBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/symfony_route_usage_test';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/symfony_route_usage_test_log';
    }
}
