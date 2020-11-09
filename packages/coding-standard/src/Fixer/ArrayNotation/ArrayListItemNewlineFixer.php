<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Fixer\AbstractArrayFixer;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\ArrayListItemNewlineFixerTest
 */
class ArrayListItemNewlineFixer extends AbstractArrayFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Indexed PHP array item has to have one line per item';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function fixArrayOpener(Tokens $tokens, BlockInfo $blockInfo, int $index): void
    {
        if (! $this->arrayAnalyzer->isIndexedList($tokens, $blockInfo)) {
            return;
        }

        $this->arrayAnalyzer->traverseArrayWithoutNesting(
            $tokens,
            $blockInfo,
            function (Token $token, int $position, Tokens $tokens): void {
                if ($token->getContent() !== ',') {
                    return;
                }

                $nextTokenPosition = $position + 1;
                $nextToken = $tokens[$nextTokenPosition] ?? null;
                if (! $nextToken instanceof Token) {
                    return;
                }

                if (Strings::contains($nextToken->getContent(), "\n")) {
                    return;
                }

                $tokens->ensureWhitespaceAtIndex($nextTokenPosition, 0, $this->whitespacesFixerConfig->getLineEnding());
            }
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$value = ['simple' => 1, 'easy' => 2];
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = ['simple' => 1,
'easy' => 2];
CODE_SAMPLE
            ),
        ]);
    }
}
