<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter\ConfiguredRuleCustomPrinter;

use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\PhpParserPhpConfigPrinter;
use Symplify\RuleDocGenerator\CaseConverter\RectorRuleCaseConverter;
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
        $return = $this->containerConfiguratorReturnClosureFactory->createFromYamlArray([
            RectorRuleCaseConverter::NAME => [
                [
                    'class' => $ruleDefinition->getRuleClass(),
                    'configuration' => $configuredCodeSample->getConfiguration(),
                ],
            ],
        ], 'Rector\Config\RectorConfig');

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }
}
