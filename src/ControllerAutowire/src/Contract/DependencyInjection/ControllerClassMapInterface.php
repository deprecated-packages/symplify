<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Contract\DependencyInjection;

interface ControllerClassMapInterface
{
    public function addController(string $id, string $class);

    /**
     * @return string[]
     */
    public function getControllers() : array;
}
