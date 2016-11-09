<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Latte;

use Latte\Engine;
use Latte\ILoader;

final class LatteFactory
{
    /**
     * @var ILoader
     */
    private $loader;

    public function __construct(ILoader $loader)
    {
        $this->loader = $loader;
    }

    public function create() : Engine
    {
        $engine = new Engine();
        $engine->setLoader($this->loader);
        $engine->setTempDirectory(sys_get_temp_dir() . '/Statie');

        return $engine;
    }
}
