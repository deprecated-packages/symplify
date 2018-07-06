<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class CollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @var DefinitionCollector
     */
    private $definitionCollector;

    public function __construct()
    {
        $this->definitionCollector = new DefinitionCollector(new DefinitionFinder());
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->collectCommandsToConsoleApplication($containerBuilder);
        $this->collectWorkersToApplication($containerBuilder);
    }

    private function collectCommandsToConsoleApplication(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            Application::class,
            Command::class,
            'add'
        );
    }

    private function collectWorkersToApplication(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            LatteToTwigConverter::class,
            CaseConverterInterface::class,
            'addCaseConverter'
        );
    }
}
