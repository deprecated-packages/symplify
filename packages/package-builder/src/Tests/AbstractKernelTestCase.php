<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

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
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

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
        $configsHash = $this->resolveConfigsHash($configs);

        $this->ensureKernelShutdown();

        $kernel = new $kernelClass('test_' . $configsHash, true);
        if (! $kernel instanceof KernelInterface) {
            throw new ShouldNotHappenException();
        }

        $this->ensureIsConfigAwareKernel($kernel);

        /** @var ExtraConfigAwareKernelInterface $kernel */
        $kernel->setConfigs($configs);

        static::$kernel = $this->bootAndReturnKernel($kernel);

        return static::$kernel;
    }

    protected function bootKernel(string $kernelClass): void
    {
        $this->ensureKernelShutdown();

        $kernel = new $kernelClass('test', true);
        if (! $kernel instanceof KernelInterface) {
            throw new ShouldNotHappenException();
        }

        static::$kernel = $this->bootAndReturnKernel($kernel);
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

    private function resolveConfigsHash(array $configs): string
    {
        $configsHash = '';
        foreach ($configs as $config) {
            $configsHash .= md5_file($config);
        }

        return md5($configsHash);
    }

    private function ensureIsConfigAwareKernel(KernelInterface $kernel): void
    {
        if ($kernel instanceof ExtraConfigAwareKernelInterface) {
            return;
        }

        throw new MissingInterfaceException(sprintf(
            '"%s" is missing an "%s" interface',
            get_class($kernel),
            ExtraConfigAwareKernelInterface::class
        ));
    }

    private function bootAndReturnKernel(KernelInterface $kernel): KernelInterface
    {
        $kernel->boot();

        $container = $kernel->getContainer();

        // private → public service hack?
        if ($container->has('test.service_container')) {
            $container = $container->get('test.service_container');
        }

        if (! $container instanceof ContainerInterface) {
            throw new ShouldNotHappenException();
        }

        // has output? keep it silent out of tests
        if ($container->has(SymfonyStyle::class)) {
            $symfonyStyle = $container->get(SymfonyStyle::class);
            $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        static::$container = $container;

        return $kernel;
    }
}
