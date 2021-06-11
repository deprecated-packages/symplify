<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection\LoaderFactory;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ConfigTransformer\Collector\XmlImportCollector;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\DependencyInjection\Loader\IdAwareXmlFileLoader;
use Symplify\ConfigTransformer\Naming\UniqueNaming;

final class IdAwareXmlFileLoaderFactory
{
    public function __construct(
        private Configuration $configuration,
        private UniqueNaming $uniqueNaming,
        private XmlImportCollector $xmlImportCollector
    ) {
    }

    public function createFromContainerBuilder(ContainerBuilder $containerBuilder): IdAwareXmlFileLoader
    {
        return new IdAwareXmlFileLoader(
            $containerBuilder,
            new FileLocator(),
            $this->configuration,
            $this->uniqueNaming,
            $this->xmlImportCollector
        );
    }
}
