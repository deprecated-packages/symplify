<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;

final class ArrayWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $endIndex;

    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(Tokens $tokens, int $startIndex, int $endIndex, TokenSkipper $tokenSkipper)
    {
        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->tokenSkipper = $tokenSkipper;
    }

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function isAssociativeArray(): bool
    {
        for ($i = $this->startIndex + 1; $i <= $this->endIndex - 1; ++$i) {
            $i = $this->tokenSkipper->skipBlocks($this->tokens, $i);

            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                return true;
            }
        }

        return false;
    }

    public function getItemCount(): int
    {
        $itemCount = 0;
        for ($i = $this->endIndex - 1; $i >= $this->startIndex; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($this->tokens, $i);

            $token = $this->tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                ++$itemCount;
            }
        }

        return $itemCount;
    }

    public function getFirstLineLength(): int
    {
        $lineLength = 0;

        // compute from here to start of line
        $currentPosition = $this->startIndex;
        while (! Strings::startsWith($this->tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($this->tokens[$currentPosition]->getContent());
            --$currentPosition;
        }

        $currentToken = $this->tokens[$currentPosition];

        // includes indent in the beginning
        $lineLength += strlen($currentToken->getContent());

        // minus end of lines, do not count PHP_EOL as characters
        $endOfLineCount = substr_count($currentToken->getContent(), PHP_EOL);
        $lineLength -= $endOfLineCount;

        // compute from here to end of line
        $currentPosition = $this->startIndex + 1;
        while (! Strings::startsWith($this->tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($this->tokens[$currentPosition]->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }
}
