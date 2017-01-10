<?php declare(strict_types=1);

namespace Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\SymplifyServiceDefinitionDecoratorBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_service_definition_decorator' . random_int(1, 100), true);
    }

    public function registerBundles() : array
    {
        return [
            new FrameworkBundle,
            new SymplifyServiceDefinitionDecoratorBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
