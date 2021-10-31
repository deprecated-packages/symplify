<?php

declare(strict_types=1);

namespace Symplify\EasyCI\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\Astral\DependencyInjection\Extension\AstralExtension;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyCIKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $extensions = [new AstralExtension()];

        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;

        return $this->create($extensions, [], $configFiles);
    }
}
