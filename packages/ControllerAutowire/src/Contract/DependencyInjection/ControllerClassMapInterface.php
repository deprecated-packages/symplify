<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Contract\DependencyInjection;

interface ControllerClassMapInterface
{
    public function addController(string $id, string $class);

    /**
     * @return string[]
     */
    public function getControllers() : array;
}
