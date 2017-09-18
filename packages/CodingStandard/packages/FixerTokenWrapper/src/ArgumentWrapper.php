<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\FixerTokenWrapper\Guard\TokenTypeGuard;

final class ArgumentWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    private function __construct(Tokens $tokens, int $index)
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_VARIABLE], self::class);

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

        return ltrim((string) $nameToken->getContent(), '$');
    }

    public function isClassType(): bool
    {
        $type = $this->getType();

        if (in_array($type, ['string', 'int', 'bool', 'null', 'array'], true)) {
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

    public function changeName(string $newName): void
    {
        $newName = Strings::startsWith($newName, '$') ?: '$' . $newName;

        $this->tokens[$this->position] = new Token([T_VARIABLE, $newName]);
    }
}
