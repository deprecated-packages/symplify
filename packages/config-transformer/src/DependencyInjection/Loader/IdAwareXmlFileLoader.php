<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection\Loader;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symplify\ConfigTransformer\Collector\XmlImportCollector;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\Naming\UniqueNaming;
use Symplify\ConfigTransformer\ValueObject\SymfonyVersionFeature;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

/**
 * Mimics https://github.com/symfony/symfony/commit/b8c68da0107a4f433dd414a355ea5589da0da0e8 for Symfony 3.3-
 */
final class IdAwareXmlFileLoader extends XmlFileLoader
{
    /**
     * @var string
     */
    private const ID = 'id';

    private PrivatesCaller $privatesCaller;

    private ?int $count = null;

    public function __construct(
        ContainerBuilder $containerBuilder,
        FileLocatorInterface $fileLocator,
        private Configuration $configuration,
        private UniqueNaming $uniqueNaming,
        private XmlImportCollector $xmlImportCollector
    ) {
        parent::__construct($containerBuilder, $fileLocator);

        $this->privatesCaller = new PrivatesCaller();
    }

    public function import(
        $resource,
        $type = null,
        $ignoreErrors = false,
        $sourceResource = null,
        $exclude = null
    ): void {
        $this->xmlImportCollector->addImport($resource, $ignoreErrors);
    }

    public function load($resource, ?string $type = null): void
    {
        $path = $this->locator->locate($resource);

        $xml = $this->privatesCaller->callPrivateMethod($this, 'parseFileToDOM', [$path]);
        $this->container->fileExists($path);

        $defaults = $this->privatesCaller->callPrivateMethod($this, 'getServiceDefaults', [$xml, $path]);
        $this->processAnonymousServices($xml, $path);

        // imports
        $this->privatesCaller->callPrivateMethod($this, 'parseImports', [$xml, $path]);

        // parameters
        $this->privatesCaller->callPrivateMethod($this, 'parseParameters', [$xml, $path]);

        // extensions
        $this->privatesCaller->callPrivateMethod($this, 'loadFromExtensions', [$xml]);

        // services
        try {
            $this->privatesCaller->callPrivateMethod($this, 'parseDefinitions', [$xml, $path, $defaults]);
        } finally {
            $this->instanceof = [];
            $this->registerAliasesForSinglyImplementedInterfaces();
        }
    }

    private function processAnonymousServices(DOMDocument $xml, string $file): void
    {
        $this->count = 0;
        $definitions = [];
        $suffix = '~' . ContainerBuilder::hash($file);

        $domxPath = new DOMXPath($xml);
        $domxPath->registerNamespace('container', self::NS);

        $definitions = $this->processAnonymousServicesInArguments($domxPath, $suffix, $file, $definitions);

        /** @var DOMNodeList<DOMNode> $nodeWithIds */
        $nodeWithIds = $domxPath->query('//container:services/container:service[@id]');
        $hasNamedServices = (bool) $nodeWithIds->length;

        // anonymous services "in the wild"
        $anonymousServiceNodes = $domxPath->query('//container:services/container:service[not(@id)]');
        if ($anonymousServiceNodes !== false) {
            /** @var DOMElement $node */
            foreach ($anonymousServiceNodes as $node) {
                $id = $this->createAnonymousServiceId($hasNamedServices, $node, $file);
                $node->setAttribute(self::ID, $id);
                $definitions[$id] = [$node, $file, true];
            }
        }

        // resolve definitions
        uksort($definitions, 'strnatcmp');

        $inversedDefinitions = array_reverse($definitions);
        foreach ($inversedDefinitions as $id => [$domElement, $file]) {
            $definition = $this->privatesCaller->callPrivateMethod(
                $this,
                'parseDefinition',
                [$domElement, $file, new Definition()]
            );

            if ($definition !== null) {
                $this->setDefinition($id, $definition);
            }
        }
    }

    /**
     * @return mixed[]
     */
    private function processAnonymousServicesInArguments(
        DOMXPath $domxPath,
        string $suffix,
        string $file,
        array $definitions
    ): array {
        $nodes = $domxPath->query(
            '//container:argument[@type="service"][not(@id)]|//container:property[@type="service"][not(@id)]|//container:bind[not(@id)]|//container:factory[not(@service)]|//container:configurator[not(@service)]'
        );

        if ($nodes !== false) {
            /** @var DOMElement $node */
            foreach ($nodes as $node) {
                // get current service id

                $parentNode = $node->parentNode;
                assert($parentNode instanceof DOMElement);

                // @see https://stackoverflow.com/a/28944/1348344
                $parentServiceId = $parentNode->getAttribute('id');

                $services = $this->privatesCaller->callPrivateMethod($this, 'getChildren', [$node, 'service']);
                if ($services !== []) {
                    $id = $this->createUniqueServiceNameFromClass($services[0], $parentServiceId);

                    $node->setAttribute(self::ID, $id);
                    $node->setAttribute('service', $id);

                    $definitions[$id] = [$services[0], $file];
                    $services[0]->setAttribute(self::ID, $id);

                    // anonymous services are always private
                    // we could not use the constant false here, because of XML parsing
                    $services[0]->setAttribute('public', 'false');
                }
            }
        }

        return $definitions;
    }

    private function createUniqueServiceNameFromClass(DOMElement $serviceDomElement, string $parentServiceId): string
    {
        $class = $serviceDomElement->getAttribute('class');
        $serviceName = $parentServiceId . '.' . $this->createServiceNameFromClass($class);

        return $this->uniqueNaming->uniquateName($serviceName);
    }

    private function createServiceNameFromClass(string $class): string
    {
        $serviceName = Strings::replace($class, '#\\\\#', '.');
        $serviceName = strtolower($serviceName);

        return $this->uniqueNaming->uniquateName($serviceName);
    }

    private function createAnonymousServiceId(bool $hasNamedServices, DOMElement $domElement, string $file): string
    {
        $className = $domElement->getAttribute('class');
        if ($hasNamedServices) {
            return $this->createServiceNameFromClass($className);
        }

        if (! $this->configuration->isAtLeastSymfonyVersion(SymfonyVersionFeature::SERVICE_WITHOUT_NAME)) {
            return $this->createServiceNameFromClass($className);
        }

        $hashedFileName = hash('sha256', $file);
        return sprintf('%d_%s', ++$this->count, $hashedFileName);
    }
}
