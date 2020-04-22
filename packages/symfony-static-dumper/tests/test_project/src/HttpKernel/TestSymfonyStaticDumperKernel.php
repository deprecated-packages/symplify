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
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;
use Symplify\Autodiscovery\Discovery;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\FlexLoader\Flex\FlexLoader;
use Symplify\SymfonyStaticDumper\SymfonyStaticDumperBundle;

final class TestSymfonyStaticDumperKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    /**
     * @var Discovery
     */
    private $discovery;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->flexLoader = new FlexLoader($environment, $this->getProjectDir());
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

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->discovery->discoverRoutes($routeCollectionBuilder);
        $this->flexLoader->loadRoutes($routeCollectionBuilder);
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->flexLoader->loadConfigs($containerBuilder, $loader);
        $this->discovery->discoverTemplates($containerBuilder);
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
