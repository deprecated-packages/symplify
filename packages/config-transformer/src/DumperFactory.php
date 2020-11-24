<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symplify\ConfigTransformer\ValueObject\Format;
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
use Symplify\PackageBuilder\Exception\NotImplementedYetException;
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
use Symplify\symplifyKernel\Exception\NotImplementedYetException;
>>>>>>> 434bcd4b3... rename Migrify to Symplify
=======
use Symplify\SymplifyKernel\Exception\NotImplementedYetException;
>>>>>>> 1a08239af... misc

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
