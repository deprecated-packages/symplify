<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class TokenRunnerKernel extends Kernel
{
    use SimpleKernelTrait;

    /**
     * @var string
     */
    private $configFile;

    public function __construct(string $configFile)
    {
        parent::__construct('token_runner', true);

        $this->configFile = $configFile;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../../src/config/config.yml');
        $loader->load($this->configFile);
    }
}
