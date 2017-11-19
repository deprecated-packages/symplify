<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tokenizer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Exception\UnexpectedTokenException;

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
     * @var Token
     */
    private $startToken;

    /**
     * @var int
     */
    private $endIndex;

    private function __construct(Tokens $tokens, int $startIndex)
    {
        $this->ensureIsArrayOpenToken($tokens[$startIndex]);

        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->startToken = $tokens[$startIndex];
    }

    public static function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): self
    {
        // validate type!

        return new self($tokens, $startIndex);
    }

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function getEndIndex(): int
    {
        if ($this->endIndex) {
            return $this->endIndex;
        }

        if ($this->isOldArray()) {
            $this->endIndex = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $this->startIndex + 1);
        } else {
            $this->endIndex = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $this->startIndex);
        }

        return $this->endIndex;
    }

    public function isOldArray(): bool
    {
        return $this->startToken->isGivenKind(T_ARRAY);
    }

    public function isAssociativeArray(): bool
    {
        for ($i = $this->startIndex + 1; $i <= $this->getEndIndex() - 1; ++$i) {
            $i = TokenSkipper::skipBlocks($this->tokens, $i);

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
        for ($i = $this->getEndIndex() - 1; $i >= $this->startIndex; --$i) {
            $i = TokenSkipper::skipBlocksReversed($this->tokens, $i);

            $token = $this->tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                ++$itemCount;
            }
        }

        return $itemCount;
    }

    private function ensureIsArrayOpenToken(Token $token): void
    {
        if ($token->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token in its constructor. "%s" token given.',
            self::class,
            implode(',', ['T_ARRAY', 'CT::T_ARRAY_SQUARE_BRACE_OPEN']),
            $token->getName()
        ));
    }
}
