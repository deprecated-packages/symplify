<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\FixerTokenWrapper\Guard\TokenTypeGuard;

final class MethodWrapper
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
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_FUNCTION], self::class);

        $this->tokens = $tokens;
        $this->index = $index;
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    /**
     * @return ArgumentWrapper[]
     */
    public function getArguments(): array
    {
        $argumentsBracketStart = $this->tokens->getNextTokenOfKind($this->index, ['(']);
        $argumentsBracketEnd = $this->tokens->findBlockEnd(
            Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
            $argumentsBracketStart
        );

        if ($argumentsBracketStart === ($argumentsBracketEnd + 1)) {
            return [];
        }

        $arguments = [];
        for ($i = $argumentsBracketStart + 1; $i < $argumentsBracketEnd; ++$i) {
            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            $arguments[] = ArgumentWrapper::createFromTokensAndPosition($this->tokens, $i);
        }

        return $arguments;
    }

    public function renameEveryVariableOccurrence(string $oldName, string $newName): void
    {
        $methodBodyStart = $this->tokens->getNextTokenOfKind($this->index, ['{']);
        $methodBodyEnd = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $methodBodyStart);

        for ($i = $methodBodyEnd - 1; $i > $methodBodyStart; --$i) {
            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            if ($token->getContent() === '$this') {
                continue;
            }

            if ($token->getContent() !== '$' . $oldName) {
                continue;
            }

            $newName = Strings::startsWith($newName, '$') ? $newName : ('$' . $newName);

            $this->tokens[$i] = new Token([T_VARIABLE, $newName]);
        }
    }
}
