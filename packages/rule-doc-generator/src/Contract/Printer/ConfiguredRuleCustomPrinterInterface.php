<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Contract\Printer;

use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

interface ConfiguredRuleCustomPrinterInterface
{
    public function printConfigureService(
        RuleDefinition $ruleDefinition,
        ConfiguredCodeSample $configuredCodeSample
    ): string;
}
