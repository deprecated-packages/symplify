<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\BetterPhpDocParser\Contract\PhpDocInfoDecoratorInterface;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class CollectDecoratorsToPhpDocInfoFactoryCompilerPass implements CompilerPassInterface
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
    }

    private function collectCommandsToConsoleApplication(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            PhpDocInfoFactory::class,
            PhpDocInfoDecoratorInterface::class,
            'addPhpDocInfoDecorator'
        );
    }
}
