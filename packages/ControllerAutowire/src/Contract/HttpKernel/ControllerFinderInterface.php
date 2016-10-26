<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Contract\HttpKernel;

interface ControllerFinderInterface
{
    /**
     * @param string[] $dirs
     *
     * @return string[]
     */
    public function findControllersInDirs(array $dirs) : array;
}
