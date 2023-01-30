<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Printer;

use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\ArrayDecorator\ServiceConfigurationDecorator;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

/**
 * @api
 * @see \Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\SmartPhpConfigPrinterTest
 */
final class SmartPhpConfigPrinter
{
    public function __construct(
        private readonly ContainerConfiguratorReturnClosureFactory $configuratorReturnClosureFactory,
        private readonly PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        private readonly ServiceConfigurationDecorator $serviceConfigurationDecorator
    ) {
    }

    /**
     * @param array<string, mixed> $configuredServices
     */
    public function printConfiguredServices(array $configuredServices): string
    {
        $servicesWithConfigureCalls = [];
        foreach ($configuredServices as $service => $configuration) {
            if ($configuration === null) {
                $servicesWithConfigureCalls[$service] = null;
            } else {
                $servicesWithConfigureCalls[$service] = $this->createServiceConfiguration($configuration, $service);
            }
        }

        $return = $this->configuratorReturnClosureFactory->createFromYamlArray([
            YamlKey::SERVICES => $servicesWithConfigureCalls,
        ]);

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    /**
     * @param mixed[] $configuration
     * @return array{calls: mixed[]}
     */
    private function createServiceConfiguration(array $configuration, string $class): array
    {
        $configuration = $this->serviceConfigurationDecorator->decorate($configuration, $class);
        return [
            'calls' => [['configure', [$configuration]]],
        ];
    }
}
