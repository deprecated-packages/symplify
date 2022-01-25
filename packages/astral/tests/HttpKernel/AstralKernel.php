<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\Astral\ValueObject\AstralConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class AstralKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = AstralConfig::FILE_PATH;
        return $this->create($configFiles, [], []);
    }
}
