<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use Symplify\PhpConfigPrinter\DependencyInjection\Extension\PhpConfigPrinterExtension;
use Symplify\PhpConfigPrinter\Dummy\DummySymfonyVersionFeatureGuard;
use Symplify\PhpConfigPrinter\Dummy\DummyYamlFileContentProvider;

/**
 * This class is dislocated in non-standard location, so it's not added by symfony/flex to bundles.php and cause app to
 * crash. See https://github.com/symplify/symplify/issues/1952#issuecomment-628765364
 */
final class PhpConfigPrinterBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $this->registerDefaultImplementations($containerBuilder);

        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    protected function createContainerExtension(): PhpConfigPrinterExtension
    {
        return new PhpConfigPrinterExtension();
    }

    private function registerDefaultImplementations(ContainerBuilder $containerBuilder): void
    {
        // set default implementations, if none provided - for better developer experience out of the box
        if (! $containerBuilder->has(YamlFileContentProviderInterface::class)) {
            $containerBuilder->autowire(DummyYamlFileContentProvider::class)
                ->setPublic(true);
            $containerBuilder->setAlias(YamlFileContentProviderInterface::class, DummyYamlFileContentProvider::class);
        }

        if (! $containerBuilder->has(SymfonyVersionFeatureGuardInterface::class)) {
            $containerBuilder->autowire(DummySymfonyVersionFeatureGuard::class)
                ->setPublic(true);
            $containerBuilder->setAlias(
                SymfonyVersionFeatureGuardInterface::class,
                DummySymfonyVersionFeatureGuard::class
            );
        }
    }
}
