<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;

final class PropertyAccessWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    private function __construct(Tokens $tokens, int $index)
    {
        $this->ensureIsPropertyAccessToken($tokens[$index]);

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

    private function ensureIsPropertyAccessToken(Token $token): void
    {
        if ($token->isGivenKind(T_VARIABLE)) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token in its constructor. "%s" token given.',
            self::class,
            implode(',', ['T_VARIABLE']),
            $token->getName()
        ));
    }
}
