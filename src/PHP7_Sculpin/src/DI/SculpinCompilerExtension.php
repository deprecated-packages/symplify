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
use Symplify\PHP7_Sculpin\Console\ConsoleApplication;
use Symplify\PHP7_Sculpin\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\PHP7_Sculpin\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\PHP7_Sculpin\DI\Helper\TypeAndCollectorTrait;
use Symplify\PHP7_Sculpin\Renderable\Routing\RouteDecorator;
use Symplify\PHP7_Sculpin\Source\SourceFileStorage;

final class SculpinCompilerExtension extends CompilerExtension
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
