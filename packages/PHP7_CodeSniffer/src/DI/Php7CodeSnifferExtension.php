<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DI;

use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Console\ConsoleApplication;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;

final class Php7CodeSnifferExtension extends CompilerExtension
{
    use ExtensionHelperTrait;

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCompile()
    {
        $this->loadSniffFactoriesToSniffSetFactory();
        $this->loadConsoleCommandsToConsoleApplication();
        $this->loadOptionResolversToConfigurationResolver();
    }

    private function loadServicesFromConfig()
    {
        $config = $this->loadFromFile(__DIR__ . '/../config/services.neon');
        $this->compiler->parseServices($this->getContainerBuilder(), $config);
    }

    private function loadConsoleCommandsToConsoleApplication()
    {
        $this->addServicesToCollector(ConsoleApplication::class, Command::class, 'add');
    }

    private function loadSniffFactoriesToSniffSetFactory()
    {
        $this->addServicesToCollector(
            SniffSetFactory::class,
            SniffFactoryInterface::class,
            'addSniffFactory'
        );
    }

    private function loadOptionResolversToConfigurationResolver()
    {
        $this->addServicesToCollector(
            ConfigurationResolver::class,
            OptionResolverInterface::class,
            'addOptionResolver'
        );
    }
}
