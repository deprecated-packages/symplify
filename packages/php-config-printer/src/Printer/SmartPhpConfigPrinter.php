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
        private ContainerConfiguratorReturnClosureFactory $configuratorReturnClosureFactory,
        private PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        private ServiceConfigurationDecorator $serviceConfigurationDecorator
    ) {
    }

    /**
     * @param array<string, mixed> $configuredServices
     */
    public function printConfiguredServices(array $configuredServices, bool $shouldUseConfigureMethod): string
    {
        $servicesWithConfigureCalls = [];
        foreach ($configuredServices as $service => $configuration) {
            if ($configuration === null) {
                $servicesWithConfigureCalls[$service] = null;
            } else {
                $servicesWithConfigureCalls[$service] = $this->createServiceConfiguration(
                    $configuration,
                    $service,
                    $shouldUseConfigureMethod
                );
            }
        }

        $return = $this->configuratorReturnClosureFactory->createFromYamlArray([
            YamlKey::SERVICES => $servicesWithConfigureCalls,
        ]);

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    /**
     * @param mixed[] $configuration
     * @return array<string, mixed>
     */
    private function createServiceConfiguration(
        array $configuration,
        string $class,
        bool $shouldUseConfigureMethod
    ): array {
        if ($shouldUseConfigureMethod) {
            $configuration = $this->serviceConfigurationDecorator->decorate(
                $configuration,
                $class,
                $shouldUseConfigureMethod
            );
            return [
                'configure' => $configuration,
            ];
        }

        $configuration = $this->serviceConfigurationDecorator->decorate($configuration, $class, false);
        return [
            'calls' => [['configure', [$configuration]]],
        ];
    }
}
