<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\MarkdownDiff\ValueObject\MarkdownDiffConfig;
use Symplify\PhpConfigPrinter\ValueObject\PhpConfigPrinterConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class RuleDocGeneratorKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = PhpConfigPrinterConfig::FILE_PATH;
        $configFiles[] = MarkdownDiffConfig::FILE_PATH;

        return $this->create([], [], $configFiles);
    }
}
