<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPCSFixer\Configurable;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SomeConfiguredFixer extends AbstractFixer implements DocumentedRuleInterface, ConfigurableRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Some description', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
bad code
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
good code
CODE_SAMPLE
                , [
                    'key' => 'value',
                ]
            )
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
    }

    public function getDefinition(): FixerDefinitionInterface
    {
    }

    public function isCandidate(Tokens $tokens): bool
    {
    }
}
