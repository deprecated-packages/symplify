<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Naming\Name\NameFactory;

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

    private function __construct(Tokens $tokens, int $index)
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_FUNCTION], __METHOD__);

        $this->tokens = $tokens;
        $this->index = $index;

        $this->bodyStart = $this->tokens->getNextTokenOfKind($this->index, ['{']);
        if ($this->bodyStart) {
            $this->bodyEnd = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $this->bodyStart);
        }
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
        $possibleInterfaceEnd = $this->tokens->getNextTokenOfKind($this->index, [';']);

        // is interface method, nothing to fix
        if ($possibleInterfaceEnd !== null && ($this->bodyStart === null || $possibleInterfaceEnd < $this->bodyStart)) {
            return;
        }

        for ($i = $this->bodyEnd - 1; $i > $this->bodyStart; --$i) {
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

    public function getDocBlockWrapper(): ?DocBlockWrapper
    {
        $docBlockToken = DocBlockFinder::findPrevious($this->tokens, $this->index);
        if ($docBlockToken === null) {
            return null;
        }

        $docBlock = new DocBlock($docBlockToken->getContent());

        return DocBlockWrapper::createFromTokensPositionAndDocBlock(
            $this->tokens,
            DocBlockFinder::findPreviousPosition($this->tokens, $this->index),
            $docBlock
        );
    }

    public function getReturnType(): ?string
    {
        for ($i = $this->index; $i < count($this->tokens); ++$i) {
            $token = $this->tokens[$i];
            if ($token->getContent() === '{') {
                return null;
            }

            if ($token->getContent() === ':') {
                $nextTokenPosition = $this->tokens->getNextMeaningfulToken($i);
                $nextToken = $this->tokens[$nextTokenPosition];

                if (! $nextToken->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
                    // nullable
                    if ($nextToken->getContent() === '?') {
                        $nextTokenPosition = $this->tokens->getNextMeaningfulToken($nextTokenPosition);
                        $nextToken = $this->tokens[$nextTokenPosition];

                        return 'null|' . $nextToken->getContent();
                    }

                    return $nextToken->getContent();
                }

                $name = NameFactory::createFromTokensAndStart($this->tokens, $nextTokenPosition);

                return $name->getName();
            }
        }

        return null;
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
}
