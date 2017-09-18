<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;

final class ArgumentWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    private function __construct(Tokens $tokens, int $index)
    {
        $this->ensureIsVariableToken($tokens[$index]);

        $this->tokens = $tokens;
        $this->position = $index;
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    public function getName(): string
    {
        $nameToken = $this->tokens[$this->position];

        return ltrim($nameToken->getContent(), '$');
    }

    public function isClassType(): bool
    {
        $type = $this->getType();

        if (in_array($type, ['string', 'int', 'bool'], true)) {
            return false;
        }

        if (Strings::contains($type, '[]')) {
            return false;
        }

        return true;
    }

    public function getType(): ?string
    {
        $previousToken = $this->tokens[$this->tokens->getPrevMeaningfulToken($this->position)];

        if (! $previousToken->isGivenKind(T_STRING)) {
            return null;
        }

        return $previousToken->getContent();
    }

    private function ensureIsVariableToken(Token $token): void
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

    public function changeName(string $newName): void
    {
        $newName = Strings::startsWith($newName, '$') ?: '$' . $newName;

        $this->tokens[$this->position] = new Token([T_VARIABLE, $newName]);
    }
}
