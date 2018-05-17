<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

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
     * @var DocBlockWrapper|null
     */
    private $docBlockWrapper;

    /**
     * @var ArgumentWrapper[]
     */
    private $argumentWrappers = [];

    /**
     * @param ArgumentWrapper[] $argumentWrappers
     */
    public function __construct(Tokens $tokens, int $index, ?DocBlockWrapper $docBlockWrapper, array $argumentWrappers)
    {
        $this->tokens = $tokens;
        $this->index = $index;

        $this->bodyStart = $this->tokens->getNextTokenOfKind($this->index, ['{']);
        if ($this->bodyStart) {
            $this->bodyEnd = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $this->bodyStart);
        }

        $this->docBlockWrapper = $docBlockWrapper;
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

            $newName = Strings::startsWith($newName, '$') ? $newName : ('$' . $newName);

            $this->tokens[$i] = new Token([T_VARIABLE, $newName]);
        }
    }

    public function getDocBlockWrapper(): ?DocBlockWrapper
    {
        return $this->docBlockWrapper;
    }

    /**
     * @return string[]
     */
    public function getReturnTypes(): array
    {
        $returnTypeAnalysis = ((new FunctionsAnalyzer())->getFunctionReturnType($this->tokens, $this->index));

        if ($returnTypeAnalysis === null) {
            return [];
        }

        $returnTypes = [];

        if (Strings::startsWith($returnTypeAnalysis->getName(), '?')) {
            // nullable type
            $returnTypes[] = 'null';
            $returnTypes[] = ltrim($returnTypeAnalysis->getName(), '?');
            return $returnTypes;
        }

        return [$returnTypeAnalysis->getName()];
    }

    /**
     * @return string[]
     */
    public function getArgumentNames(): array
    {
        $argumentNames = [];
        foreach ($this->getArguments() as $argument) {
            $argumentNames[] = $argument->getName();
        }

        return $argumentNames;
    }

    private function shouldSkip(string $oldName, Token $token): bool
    {
        if ($token->isGivenKind(T_VARIABLE) === false) {
            return true;
        }

        if ($token->getContent() === '$this') {
            return true;
        }

        if ($token->getContent() !== '$' . $oldName) {
            return true;
        }

        return false;
    }
}
