<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection\LoaderFactory;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ConfigTransformer\Collector\XmlImportCollector;
use Symplify\ConfigTransformer\DependencyInjection\Loader\IdAwareXmlFileLoader;
use Symplify\ConfigTransformer\Naming\UniqueNaming;
use Symplify\ConfigTransformer\ValueObject\Configuration;

final class IdAwareXmlFileLoaderFactory
{
    public function __construct(
        private UniqueNaming $uniqueNaming,
        private XmlImportCollector $xmlImportCollector
    ) {
    }

    public function createFromContainerBuilder(
        ContainerBuilder $containerBuilder,
        Configuration $configuration
    ): IdAwareXmlFileLoader {
        return new IdAwareXmlFileLoader(
            $containerBuilder,
            new FileLocator(),
            $configuration,
            $this->uniqueNaming,
            $this->xmlImportCollector
        );
    }
}
