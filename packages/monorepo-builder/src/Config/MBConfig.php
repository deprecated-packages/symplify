<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Webmozart\Assert\Assert;

final class MBConfig extends ContainerConfigurator
{
    /**
     * @param string[] $packageDirectories
     */
    public function packageDirectories(array $packageDirectories): void
    {
        Assert::allString($packageDirectories);
        Assert::allFileExists($packageDirectories);

        $parameters = $this->parameters();
        $parameters->set(Option::PACKAGE_DIRECTORIES, $packageDirectories);
    }
}
