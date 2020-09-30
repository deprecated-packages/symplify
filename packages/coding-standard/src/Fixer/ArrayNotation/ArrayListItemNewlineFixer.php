<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\ArrayListItemNewlineFixerTest
 */
class ArrayListItemNewlineFixer extends AbstractArrayFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Indexed PHP array item has to have one line per item', []);
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
                $nextMeaningfulToken = $tokens[$nextTokenPosition] ?? null;
                if (! $nextMeaningfulToken instanceof Token) {
                    return;
                }

                $tokens->ensureWhitespaceAtIndex($nextTokenPosition, 0, $this->whitespacesFixerConfig->getLineEnding());
            }
        );
    }
}
