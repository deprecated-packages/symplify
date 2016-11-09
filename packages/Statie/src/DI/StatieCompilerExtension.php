<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;
use Symplify\Statie\Console\ConsoleApplication;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\DI\Helper\TypeAndCollectorTrait;
use Symplify\Statie\Renderable\Routing\RouteDecorator;
use Symplify\Statie\Source\SourceFileStorage;

final class StatieCompilerExtension extends CompilerExtension
{
    use TypeAndCollectorTrait;

    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();
    }

    public function beforeCompile()
    {
        $this->collectByType(ConsoleApplication::class, Command::class, 'add');
        $this->collectByType(SourceFileStorage::class, SourceFileFilterInterface::class, 'addSourceFileFilter');
        $this->collectByType(RouteDecorator::class, RouteInterface::class, 'addRoute');
    }

    private function loadServicesFromConfig()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
        );
    }
}
