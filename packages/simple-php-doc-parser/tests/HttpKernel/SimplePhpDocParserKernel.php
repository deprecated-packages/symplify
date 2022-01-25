<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\SimplePhpDocParser\ValueObject\SimplePhpDocParserConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SimplePhpDocParserKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = SimplePhpDocParserConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
