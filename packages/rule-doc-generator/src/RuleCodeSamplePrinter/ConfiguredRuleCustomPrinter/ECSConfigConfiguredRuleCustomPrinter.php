<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\RuleCodeSamplePrinter\ConfiguredRuleCustomPrinter;

use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\PhpParserPhpConfigPrinter;
use Symplify\RuleDocGenerator\CaseConverter\ECSRuleCaseConverter;
use Symplify\RuleDocGenerator\Contract\Printer\ConfiguredRuleCustomPrinterInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ECSConfigConfiguredRuleCustomPrinter implements ConfiguredRuleCustomPrinterInterface
{
    public function __construct(
        private readonly ContainerConfiguratorReturnClosureFactory $containerConfiguratorReturnClosureFactory,
        private readonly PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
    ) {
    }

    public function printConfigureService(
        RuleDefinition $ruleDefinition,
        ConfiguredCodeSample $configuredCodeSample
    ): string {
        $return = $this->containerConfiguratorReturnClosureFactory->createFromYamlArray([
            ECSRuleCaseConverter::NAME => [
                [
                    'class' => $ruleDefinition->getRuleClass(),
                    'configuration' => $configuredCodeSample->getConfiguration(),
                ],
            ],
        ], 'Symplify\EasyCodingStandard\Config\ECSConfig');

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }
}
