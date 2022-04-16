<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter;

use Rector\Config\RectorConfig;
use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\PhpParserPhpConfigPrinter;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;
use Symplify\RuleDocGenerator\Contract\Printer\ConfiguredRuleCustomPrinterInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RectorConfigConfiguredRuleCustomPrinter implements ConfiguredRuleCustomPrinterInterface
{
    public function __construct(
        private ContainerConfiguratorReturnClosureFactory $containerConfiguratorReturnClosureFactory,
        private PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
    ) {
    }

    public function printConfigureService(
        RuleDefinition $ruleDefinition,
        ConfiguredCodeSample $configuredCodeSample
    ): string {
        dump($ruleDefinition);
        dump($configuredCodeSample);

        $return = $this->containerConfiguratorReturnClosureFactory->createFromYamlArray([
            YamlKey::SERVICES => [],
            // @todo
        ], RectorConfig::class);

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }
}
