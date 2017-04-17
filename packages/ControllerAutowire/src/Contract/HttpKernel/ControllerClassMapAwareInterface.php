<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Contract\HttpKernel;

interface ControllerClassMapAwareInterface
{
    /**
     * @param string[] $controllerClassMap
     */
    public function setControllerClassMap(array $controllerClassMap): void;
}
