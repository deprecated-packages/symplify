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
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var UniqueNaming
     */
    private $uniqueNaming;

    /**
     * @var XmlImportCollector
     */
    private $xmlImportCollector;

    public function __construct(
        Configuration $configuration,
        UniqueNaming $uniqueNaming,
        XmlImportCollector $xmlImportCollector
    ) {
        $this->configuration = $configuration;
        $this->uniqueNaming = $uniqueNaming;
        $this->xmlImportCollector = $xmlImportCollector;
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
