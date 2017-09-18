<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\FixerTokenWrapper\Guard\TokenTypeGuard;

final class PropertyAccessWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var int
     */
    private $index;

    private function __construct(Tokens $tokens, int $index)
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_VARIABLE], self::class);

        $this->index = $index;
        $this->tokens = $tokens;
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    public function getName(): string
    {
        $propertyNameToken = $this->tokens[$this->getPropertyNamePosition()];

        return $propertyNameToken->getContent();
    }

    public function changeName(string $newName): void
    {
        $this->tokens[$this->getPropertyNamePosition()] = new Token([T_STRING, $newName]);
    }

    private function getPropertyNamePosition(): ?int
    {
        return $this->tokens->getNextMeaningfulToken($this->index + 1);
    }
}
