<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\CodingStandard\ValueObject\SymplifyCodingStandardConfig;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\EasyCodingStandardExtension;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SymplifyCodingStandardKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $extensions = [new EasyCodingStandardExtension()];
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = SymplifyCodingStandardConfig::FILE_PATH;

        return $this->create($extensions, [], $configFiles);
    }
}
