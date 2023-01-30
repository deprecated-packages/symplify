<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\ValueObject;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
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
        private readonly string $kernelClass,
        private readonly array $extraConfigs = []
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
        return new $kernelClass($environment, StaticInputDetector::isDebug());
    }

    private function booKernelAndRunApplication(): void
    {
        $kernel = $this->createKernel();

        if ($kernel instanceof LightKernelInterface) {
            $container = $kernel->createFromConfigs($this->extraConfigs);
        } else {
            $kernel->boot();
            $container = $kernel->getContainer();
        }

        /** @var Application $application */
        $application = $container->get(Application::class);
        // remove --no-interaction (with -n shortcut) option from Application
        // because we need to create option with -n shortcuts too
        // for example: --dry-run with shortcut -n
        $inputDefinition = $application->getDefinition();

        $options = $inputDefinition->getOptions();
        $options = array_filter($options, static fn ($option): bool => $option->getName() !== 'no-interaction');

        $inputDefinition->setOptions($options);

        exit($application->run());
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

        $currentValueType = get_debug_type($kernelClass);

        $errorMessage = sprintf(
            'Class "%s" must by type of "%s" or "%s". "%s" given',
            $kernelClass,
            KernelInterface::class,
            LightKernelInterface::class,
            $currentValueType
        );

        throw new BootException($errorMessage);
    }
}
