<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Printer;

use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\ArrayDecorator\ServiceConfigurationDecorator;

/**
 * @see \Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\SmartPhpConfigPrinterTest
 */
final class SmartPhpConfigPrinter
{
    /**
     * @var ContainerConfiguratorReturnClosureFactory
     */
    private $configuratorReturnClosureFactory;

    /**
     * @var PhpParserPhpConfigPrinter
     */
    private $phpParserPhpConfigPrinter;

    /**
     * @var ServiceConfigurationDecorator
     */
    private $serviceConfigurationDecorator;

    public function __construct(
        ContainerConfiguratorReturnClosureFactory $configuratorReturnClosureFactory,
        PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        ServiceConfigurationDecorator $serviceConfigurationDecorator
    ) {
        $this->configuratorReturnClosureFactory = $configuratorReturnClosureFactory;
        $this->phpParserPhpConfigPrinter = $phpParserPhpConfigPrinter;
        $this->serviceConfigurationDecorator = $serviceConfigurationDecorator;
    }

    /**
     * @param array<string, mixed[]|null> $configuredServices
     */
    public function printConfiguredServices(array $configuredServices): string
    {
        $servicesWithConfigureCalls = [];
        foreach ($configuredServices as $service => $configuration) {
            $servicesWithConfigureCalls[$service] = $this->createServiceConfiguration($configuration, $service);
        }

        $return = $this->configuratorReturnClosureFactory->createFromYamlArray(
            [
                'services' => $servicesWithConfigureCalls,
            ]
        );

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    /**
     * @param mixed[]|null $configuration
     */
    private function createServiceConfiguration(?array $configuration, string $class): ?array
    {
        if ($configuration === null) {
            return null;
        }
        if ($configuration === []) {
            return null;
        }
        $configuration = $this->serviceConfigurationDecorator->decorate($configuration, $class);

        return [
            'calls' => [['configure', [$configuration]]],
        ];
    }
}
