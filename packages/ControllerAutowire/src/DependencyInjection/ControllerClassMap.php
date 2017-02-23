<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection;

use Symplify\ControllerAutowire\Contract\DependencyInjection\ControllerClassMapInterface;

final class ControllerClassMap implements ControllerClassMapInterface
{
    /**
     * @var string[]
     */
    private $controllers = [];

    public function addController(string $id, string $class): void
    {
        $this->controllers[$id] = $class;
    }

    /**
     * @return string[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }
}
