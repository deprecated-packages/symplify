<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
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

    /**
     * @var int
     */
    private $argumentsBracketStart;

    /**
     * @var int
     */
    private $argumentsBracketEnd;

    /**
     * @var DocBlockWrapper|null
     */
    private $docBlockWrapper;

    private function __construct(Tokens $tokens, int $index)
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_FUNCTION], __METHOD__);

        $this->tokens = $tokens;
        $this->index = $index;

        $docBlockPosition = DocBlockFinder::findPreviousPosition($this->tokens, $this->index);
        if ($docBlockPosition) {
            $this->docBlockWrapper = DocBlockWrapper::createFromTokensAndPosition($this->tokens, $docBlockPosition);
        }

        $this->bodyStart = $this->tokens->getNextTokenOfKind($this->index, ['{']);
        if ($this->bodyStart) {
            $this->bodyEnd = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $this->bodyStart);
        }

        $this->argumentsBracketStart = $this->tokens->getNextTokenOfKind($this->index, ['(']);
        $this->argumentsBracketEnd = $this->tokens->findBlockEnd(
            Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
            $this->argumentsBracketStart
        );
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
        return $this->docBlockWrapper;
    }

    public function getReturnType(): ?string
    {
        $tokenCount = count($this->tokens);
        for ($i = $this->index; $i < $tokenCount; ++$i) {
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

    public function getFirstLineLength(): int
    {
        $lineLength = 0;

        // compute from here to start of line
        $currentPosition = $this->index;
        while (! Strings::startsWith($this->tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($this->tokens[$currentPosition]->getContent());
            --$currentPosition;
        }

        $currentToken = $this->tokens[$currentPosition];

        // includes indent in the beginning
        $lineLength += strlen($currentToken->getContent());

        // minus end of lines, do not count PHP_EOL as characters
        $endOfLineCount = substr_count($currentToken->getContent(), PHP_EOL);
        $lineLength -= $endOfLineCount;

        // compute from here to end of line or till the start " use (...) "
        $currentPosition = $this->index + 1;
        while (! $this->isEndOFArgumentsLine($currentPosition)) {
            $lineLength += strlen($this->tokens[$currentPosition]->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }

    public function getArgumentsBracketStart(): int
    {
        return $this->argumentsBracketStart;
    }

    public function getArgumentsBracketEnd(): int
    {
        return $this->argumentsBracketEnd;
    }

    public function getLineLengthToEndOfArguments(): int
    {
        $lineLength = 0;

        // compute from function to start of line
        $currentPosition = $this->index;
        while (! Strings::startsWith($this->tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($this->tokens[$currentPosition]->getContent());
            --$currentPosition;
        }

        // get length from start of function till end of arguments - with spaces as one
        $currentPosition = $this->index;
        while ($currentPosition < $this->argumentsBracketEnd) {
            $currentToken = $this->tokens[$currentPosition];
            if ($currentToken->isGivenKind(T_WHITESPACE)) {
                $lineLength += 1;
                ++$currentPosition;
                continue;
            }

            $lineLength += strlen($this->tokens[$currentPosition]->getContent());
            ++$currentPosition;
        }

        // get length from end or arguments to first line break
        $currentPosition = $this->argumentsBracketEnd;
        while (! Strings::startsWith($this->tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $currentToken = $this->tokens[$currentPosition];

            $lineLength += strlen($currentToken->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }

    private function isEndOFArgumentsLine(int $currentPosition): bool
    {
        if (Strings::startsWith($this->tokens[$currentPosition]->getContent(), PHP_EOL)) {
            return true;
        }

        return $this->tokens[$currentPosition]->isGivenKind(CT::T_USE_LAMBDA);
    }
}
