<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\ValueObject\Option;
use Webmozart\Assert\Assert;

final class EasyCIConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function excludeCheckPaths(array $paths): void
    {
        Assert::allString($paths);

        $parameters = $this->parameters();
        $parameters->set(Option::EXCLUDED_CHECK_PATHS, $paths);
    }

    /**
     * @param string[] $typesToSkip
     */
    public function typesToSkip(array $typesToSkip): void
    {
        Assert::allString($typesToSkip);

        $parameters = $this->parameters();
        $parameters->set(Option::TYPES_TO_SKIP, $typesToSkip);
    }
}
