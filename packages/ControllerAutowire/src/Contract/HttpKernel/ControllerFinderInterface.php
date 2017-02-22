<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Contract\HttpKernel;

interface ControllerFinderInterface
{
    /**
     * @param string[] $dirs
     *
     * @return string[]
     */
    public function findControllersInDirs(array $dirs): array;
}
