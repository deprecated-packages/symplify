<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;
use Symplify\PHP7_Sculpin\Console\Application;
use Symplify\PHP7_Sculpin\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\PHP7_Sculpin\DI\Helper\TypeAndCollectorTrait;
use Symplify\PHP7_Sculpin\Source\SourceFileStorage;

final class SculpinCompilerExtension extends CompilerExtension
{
    use TypeAndCollectorTrait;

    /**
     * @var string[]
     */
    private $defaultConfig = [
        'sourceDir' => '%appDir%/../../../source',
        'outputDir' => '%appDir%/../../../output',
        'postRoute' => 'blog/:year/:month/:day/:filename',
    ];

    public function loadConfiguration()
    {
        $config = $this->validateAndReturnConfig($this->defaultConfig);
        $this->loadConfigToParameters($config);
        $this->loadServicesFromConfig();
    }

    public function beforeCompile()
    {
        $this->collectByType(Application::class, Command::class, 'add');
        $this->collectByType(SourceFileStorage::class, SourceFileFilterInterface::class, 'addSourceFileFilter');
    }

    private function validateAndReturnConfig(array $defaultConfig) : array
    {
        $defaultConfig = $this->validateConfig($defaultConfig);
        $defaultConfig['sourceDir'] = $this->expandParameter($defaultConfig['sourceDir']);

        $defaultConfig['sourceDir'] = realpath($defaultConfig['sourceDir']);

        return $defaultConfig;
    }

    private function loadConfigToParameters(array $config)
    {
        $this->getContainerBuilder()->parameters += $config;
    }

    private function loadServicesFromConfig()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__.'/../config/services.neon')['services']
        );
    }

    private function expandParameter(string $value) : string
    {
        return $this->getContainerBuilder()
            ->expand($value);
    }
}
