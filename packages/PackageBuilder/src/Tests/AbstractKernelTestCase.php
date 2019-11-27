<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

use Nette\Utils\Json;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Service\ResetInterface;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\PackageBuilder\Exception\HttpKernel\MissingInterfaceException;

/**
 * Inspiration
 * @see https://github.com/symfony/symfony/blob/master/src/Symfony/Bundle/FrameworkBundle/Test/KernelTestCase.php
 */
abstract class AbstractKernelTestCase extends TestCase
{
    /**
     * @var KernelInterface
     */
    protected static $kernel;

    /**
     * @var ContainerInterface|Container
     */
    protected static $container;

    /**
     * @param string[] $configs
     */
    protected function bootKernelWithConfigs(string $kernelClass, array $configs): KernelInterface
    {
        $configsHash = md5(Json::encode($configs));

        $this->ensureKernelShutdown();
        static::$kernel = new $kernelClass('test_' . $configsHash, true);

        $this->ensureIsConfigAwareKernel();

        static::$kernel->setConfigs($configs);
        static::$kernel = $this->bootAndReturnKernel();

        return static::$kernel;
    }

    protected function bootKernel(string $kernelClass): KernelInterface
    {
        $this->ensureKernelShutdown();

        static::$kernel = new $kernelClass('test', true);
        static::$kernel = $this->bootAndReturnKernel();

        return static::$kernel;
    }

    /**
     * Shuts the kernel down if it was used in the test.
     */
    protected function ensureKernelShutdown(): void
    {
        if (static::$kernel !== null) {
            // make sure boot() is called
            // @see https://github.com/symfony/symfony/pull/31202/files
            $container = (new ReflectionClass(static::$kernel))->getProperty('container');
            $container->setAccessible(true);
            if ($container->getValue(static::$kernel) !== null) {
                $container = static::$kernel->getContainer();
                static::$kernel->shutdown();
                if ($container instanceof ResetInterface) {
                    $container->reset();
                }
            }
        }

        static::$container = null;
    }

    private function ensureIsConfigAwareKernel(): void
    {
        if (static::$kernel instanceof ExtraConfigAwareKernelInterface) {
            return;
        }

        throw new MissingInterfaceException(sprintf(
            '"%s" is missing an "%s" interface',
            static::class,
            ExtraConfigAwareKernelInterface::class
        ));
    }

    private function bootAndReturnKernel(): KernelInterface
    {
        static::$kernel->boot();

        $container = static::$kernel->getContainer();

        // private → public service hack?
        static::$container = $container->has('test.service_container') ?
            $container->get('test.service_container') : $container;

        // has output? keep it silent out of tests
        if (static::$container->has(SymfonyStyle::class)) {
            $symfonyStyle = static::$container->get(SymfonyStyle::class);
            $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return static::$kernel;
    }
}
