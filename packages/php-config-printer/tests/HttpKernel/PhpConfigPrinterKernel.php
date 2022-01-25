<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\PhpConfigPrinter\ValueObject\PhpConfigPrinterConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class PhpConfigPrinterKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = PhpConfigPrinterConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
