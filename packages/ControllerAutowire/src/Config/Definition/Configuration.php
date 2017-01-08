<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Config\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var string[]
     */
    private $defaultControllerDirs = ['%kernel.root_dir%', '%kernel.root_dir%/../src'];

    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root(SymplifyControllerAutowireBundle::ALIAS);

        $rootNode->children()
            ->arrayNode('controller_dirs')
                ->defaultValue($this->defaultControllerDirs)
                ->prototype('scalar')
            ->end();

        return $treeBuilder;
    }
}
