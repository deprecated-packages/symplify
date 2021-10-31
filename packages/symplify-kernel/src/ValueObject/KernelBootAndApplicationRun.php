<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\ValueObject;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\Exception\BootException;
use Throwable;

/**
 * @api
 */
final class KernelBootAndApplicationRun
{
    /**
     * @param class-string<KernelInterface|LightKernelInterface> $kernelClass
     * @param string[] $extraConfigs
     */
    public function __construct(
        private string $kernelClass,
        private array $extraConfigs = []
    ) {
        $this->validateKernelClass($this->kernelClass);
    }

    public function run(): void
    {
        try {
            $this->booKernelAndRunApplication();
        } catch (Throwable $throwable) {
            $symfonyStyleFactory = new SymfonyStyleFactory();
            $symfonyStyle = $symfonyStyleFactory->create();
            $symfonyStyle->error($throwable->getMessage());
            exit(Command::FAILURE);
        }
    }

    private function createKernel(): KernelInterface|LightKernelInterface
    {
        // random has is needed, so cache is invalidated and changes from config are loaded
        $kernelClass = $this->kernelClass;

        if (is_a($kernelClass, LightKernelInterface::class, true)) {
            return new $kernelClass();
        }

        $environment = 'prod' . random_int(1, 100000);
        $kernel = new $kernelClass($environment, StaticInputDetector::isDebug());

        $this->setExtraConfigs($kernel, $kernelClass);

        return $kernel;
    }

    private function booKernelAndRunApplication(): void
    {
        $kernel = $this->createKernel();

        if ($kernel instanceof LightKernelInterface) {
            $container = $kernel->createFromConfigs($this->extraConfigs);
        } else {
            if ($kernel instanceof ExtraConfigAwareKernelInterface && $this->extraConfigs !== []) {
                $kernel->setConfigs($this->extraConfigs);
            }

            $kernel->boot();
            $container = $kernel->getContainer();
        }

        /** @var Application $application */
        $application = $container->get(Application::class);
        exit($application->run());
    }

    private function setExtraConfigs(KernelInterface $kernel, string $kernelClass): void
    {
        if ($this->extraConfigs === []) {
            return;
        }

        if (is_a($kernel, ExtraConfigAwareKernelInterface::class, true)) {
            /** @var ExtraConfigAwareKernelInterface $kernel */
            $kernel->setConfigs($this->extraConfigs);
        } else {
            $message = sprintf(
                'Extra configs are set, but the "%s" kernel class is missing "%s" interface',
                $kernelClass,
                ExtraConfigAwareKernelInterface::class
            );
            throw new BootException($message);
        }
    }

    /**
     * @param class-string $kernelClass
     */
    private function validateKernelClass(string $kernelClass): void
    {
        if (is_a($kernelClass, KernelInterface::class, true)) {
            return;
        }

        if (is_a($kernelClass, LightKernelInterface::class, true)) {
            return;
        }

        $errorMessage = sprintf(
            'Class "%s" must by type of "%s" or "%s"',
            $kernelClass,
            KernelInterface::class,
            LightKernelInterface::class
        );
        throw new BootException($errorMessage);
    }
}
