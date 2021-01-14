<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\HttpKernel;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Discovery;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymfonyStaticDumper\SymfonyStaticDumperBundle;

final class TestSymfonyStaticDumperKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var Discovery
     */
    private $discovery;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->discovery = new Discovery($this->getProjectDir());
    }

    public function getProjectDir(): string
    {
        return __DIR__ . '/../..';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new FrameworkBundle(), new TwigBundle(), new SymfonyStaticDumperBundle()];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/test_symfony_static_dumper_kernel_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/test_symfony_static_dumper_kernel_log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/services.php');
        $loader->load(__DIR__ . '/../../config/packages/twig.php');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->discovery->discoverTemplates($containerBuilder);
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $routeCollectionBuilder->import(__DIR__ . '/../../config/routes.php');
    }
}
