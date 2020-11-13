<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\RemoveCommentedCodeFixerTest
 */
final class RemoveCommentedCodeFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @see https://regex101.com/r/uMDMtt/2
     * @var string
     */
    private const COMMENTED_CODE_REGEX = '#\/\/\s+(\$.*|.*\(.*?\));$#';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove commented code like "// $one = 1000;" comment';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $reverseTokens = $this->reverseTokens($tokens);
        foreach ($reverseTokens as $index => $token) {
            if (! $token->isGivenKind([T_COMMENT])) {
                continue;
            }

            $originalDocContent = $token->getContent();
            $cleanedDocContent = Strings::replace($originalDocContent, self::COMMENTED_CODE_REGEX, '');
            if ($cleanedDocContent !== '') {
                continue;
            }

            // remove token
            $tokens->clearAt($index);
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// $one = 1;
// $two = 2;
// $three = 3;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// note
CODE_SAMPLE
            ),
        ]);
    }
}
