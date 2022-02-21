<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\PhpDocParser\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\Astral\PhpDocParser\ValueObject\SimplePhpDocParserConfig;
use Symplify\Astral\ValueObject\AstralConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SimplePhpDocParserKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = AstralConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
