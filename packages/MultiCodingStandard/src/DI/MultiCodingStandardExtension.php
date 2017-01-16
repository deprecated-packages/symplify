<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class MultiCodingStandardExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $this->loadServicesFromConfigPath(__DIR__.'/../config/services.neon');
    }

    private function loadServicesFromConfigPath(string $configPath)
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile($configPath)['services']
        );
    }
}
