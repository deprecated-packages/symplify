<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class MethodWrapper
{
    /**
     * @var int
     */
    private $index;

    /**
     * Interface method has no body
     *
     * @var int|null
     */
    private $bodyStart;

    /**
     * Interface method has no body
     *
     * @var int|null
     */
    private $bodyEnd;

    /**
     * @var ArgumentWrapper[]
     */
    private $argumentWrappers = [];

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @param ArgumentWrapper[] $argumentWrappers
     */
    public function __construct(Tokens $tokens, int $index, array $argumentWrappers)
    {
        $this->tokens = $tokens;
        $this->index = $index;

        $this->bodyStart = $this->tokens->getNextTokenOfKind($this->index, ['{']);
        if ($this->bodyStart) {
            $this->bodyEnd = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $this->bodyStart);
        }

        $this->argumentWrappers = $argumentWrappers;
    }

    /**
     * @return ArgumentWrapper[]
     */
    public function getArguments(): array
    {
        return $this->argumentWrappers;
    }

    public function renameEveryVariableOccurrence(string $oldName, string $newName): void
    {
        $possibleInterfaceEnd = $this->tokens->getNextTokenOfKind($this->index, [';']);

        // is interface method, nothing to fix
        if ($possibleInterfaceEnd !== null && ($this->bodyStart === null || $possibleInterfaceEnd < $this->bodyStart)) {
            return;
        }

        for ($i = $this->bodyEnd - 1; $i > $this->bodyStart; --$i) {
            $token = $this->tokens[$i];
            if ($this->shouldSkip($oldName, $token)) {
                continue;
            }

            $newName = Strings::startsWith($newName, '$') ? $newName : '$' . $newName;

            $this->tokens[$i] = new Token([T_VARIABLE, $newName]);
        }
    }

    private function shouldSkip(string $oldName, Token $token): bool
    {
        if (! $token->isGivenKind(T_VARIABLE)) {
            return true;
        }

        if ($token->getContent() === '$this') {
            return true;
        }
        return $token->getContent() !== '$' . $oldName;
    }
}
