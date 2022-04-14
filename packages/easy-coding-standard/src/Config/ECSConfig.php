<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Webmozart\Assert\Assert;

final class ECSConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function paths(array $paths): void
    {
        Assert::allString($paths);

        $parameters = $this->parameters();
        $parameters->set(Option::PATHS, $paths);
    }

    /**
     * @param mixed[] $skips
     */
    public function skip(array $skips): void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::SKIP, $skips);
    }

    /**
     * @param string[] $sets
     */
    public function sets(array $sets): void
    {
        Assert::allString($sets);
        Assert::allFileExists($sets);

        foreach ($sets as $set) {
            $this->import($set);
        }
    }
}
