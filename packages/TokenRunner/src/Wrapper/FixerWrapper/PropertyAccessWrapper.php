<?php declare(strict_types=1);

namespace Symplify\TokenRunner\FixerTokenWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\FixerTokenWrapper\Guard\TokenTypeGuard;

final class PropertyAccessWrapper extends AbstractVariableWrapper
{
    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$position], [T_VARIABLE], self::class);

        return new self($tokens, $position);
    }

    public function changeName(string $newName): void
    {
        $this->changeNameWithTokenType($newName, T_STRING);
    }

    protected function getNamePosition(): ?int
    {
        return $this->tokens->getNextMeaningfulToken($this->index + 1);
    }
}
