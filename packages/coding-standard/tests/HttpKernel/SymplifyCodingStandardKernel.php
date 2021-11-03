<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
use Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SymplifyCodingStandardKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = EasyCodingStandardConfig::FILE_PATH;
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = CodingStandardConfig::FILE_PATH;

        return $this->create([], [], $configFiles);
    }
}
