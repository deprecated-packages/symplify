<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

final class LineLengthResolver
{
    /**
     * @param Tokens|Token[] $tokens
     */
    public function getLengthFromStartEnd(BlockInfo $blockInfo, Tokens $tokens): int
    {
        $lineLength = 0;

        // compute from function to start of line
        $currentPosition = $blockInfo->getStart();
        while (! $this->isNewLineOrOpenTag($tokens, $currentPosition)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            --$currentPosition;

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        // get spaces to first line
        $lineLength += strlen($tokens[$currentPosition]->getContent());

        // get length from start of function till end of arguments - with spaces as one
        $lineLength += $this->getLenthFromFunctionStartToEndOfArguments($blockInfo, $tokens);

        // get length from end or arguments to first line break
        $lineLength += $this->getLengthFromEndOfArgumentToLineBreak($blockInfo, $tokens);

        return $lineLength;
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    private function isNewLineOrOpenTag(Tokens $tokens, int $position): bool
    {
        /** @var Token $currentToken */
        $currentToken = $tokens[$position];

        if (Strings::startsWith($currentToken->getContent(), StaticEolConfiguration::getEolChar())) {
            return true;
        }

        return $currentToken->isGivenKind(T_OPEN_TAG);
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    private function getLenthFromFunctionStartToEndOfArguments(BlockInfo $blockInfo, Tokens $tokens): int
    {
        $length = 0;

        $currentPosition = $blockInfo->getStart();

        while ($currentPosition < $blockInfo->getEnd()) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$currentPosition];

            if ($currentToken->isGivenKind(T_WHITESPACE)) {
                ++$length;
                ++$currentPosition;
                continue;
            }

            $length += strlen($currentToken->getContent());
            ++$currentPosition;

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return $length;
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    private function getLengthFromEndOfArgumentToLineBreak(BlockInfo $blockInfo, Tokens $tokens): int
    {
        $length = 0;

        $currentPosition = $blockInfo->getEnd();

        /** @var Token $currentToken */
        $currentToken = $tokens[$currentPosition];

        while (! Strings::startsWith($currentToken->getContent(), StaticEolConfiguration::getEolChar())) {
            $length += strlen($currentToken->getContent());
            ++$currentPosition;

            if (! isset($tokens[$currentPosition])) {
                break;
            }

            /** @var Token $currentToken */
            $currentToken = $tokens[$currentPosition];
        }

        return $length;
    }
}
