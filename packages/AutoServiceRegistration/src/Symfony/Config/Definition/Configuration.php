<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\AutoServiceRegistration\Symfony\Config\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symplify\AutoServiceRegistration\Symfony\SymplifyAutoServiceRegistrationBundle;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    const DIRECTORIES_TO_SCAN = 'directories_to_scan';

    /**
     * @var string
     */
    const CLASS_SUFFIXES_TO_SEEK = 'class_suffixes_to_seek';

    /**
     * @var string[]
     */
    private $defaultDirectoriesToScan = [
        '%kernel.root_dir%',
        '%kernel.root_dir%/../src',
    ];

    /**
     * @var string[]
     */
    private $defaultClassSuffixesToSeek = ['Controller'];

    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(SymplifyAutoServiceRegistrationBundle::ALIAS);

        $rootNode->children()
            ->arrayNode(self::DIRECTORIES_TO_SCAN)
                ->defaultValue($this->defaultDirectoriesToScan)
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode(self::CLASS_SUFFIXES_TO_SEEK)
                ->defaultValue($this->defaultClassSuffixesToSeek)
                ->prototype('scalar')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
