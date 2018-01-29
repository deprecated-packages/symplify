<?php declare(strict_types=1);

namespace Symplify\Monorepo\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;

final class CollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            Application::class,
            Command::class,
            'add'
        );
    }
}
