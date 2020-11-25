<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\PackageBuilder\Exception\NotImplementedYetException;

final class DumperFactory
{
    public function createFromContainerBuilderAndOutputFormat(
        ContainerBuilder $containerBuilder,
        string $outputFormat
    ): Dumper {
        if ($outputFormat === Format::YAML) {
            return new YamlDumper($containerBuilder);
        }

        throw new NotImplementedYetException($outputFormat);
    }
}
