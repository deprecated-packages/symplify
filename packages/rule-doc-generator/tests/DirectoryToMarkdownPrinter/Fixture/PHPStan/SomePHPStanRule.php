<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\PHPStan;

use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SomePHPStanRule implements Rule, DocumentedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Some description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
bad code
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
good code
CODE_SAMPLE
            )
        ]);
    }

    public function getNodeType(): string
    {
    }

    public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
    {
    }
}
