<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class PackageBuilderTestKernel extends Kernel
{
    use SimpleKernelTrait;

    /**
     * @var string
     */
    private $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
        parent::__construct($this->getUniqueKernelKey() . random_int(1, 10000), true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->configPath);
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->autowire(ParameterProvider::class)
            ->setPublic(true);
    }
}
