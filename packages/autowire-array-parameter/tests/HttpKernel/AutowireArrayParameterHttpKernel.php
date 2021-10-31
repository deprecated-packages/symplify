<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class AutowireArrayParameterHttpKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../config/autowire_array_parameter.php';

        return $this->create([], [], $configFiles);
    }
}
