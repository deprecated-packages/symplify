<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Naming\Name\NameFactory;

final class ClassWrapper
{
    /**
     * @var int
     */
    private $startBracketIndex;

    /**
     * @var int
     */
    private $endBracketIndex;

    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var Token
     */
    private $classToken;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var mixed[]
     */
    private $classyElements = [];

    private function __construct(Tokens $tokens, int $startIndex)
    {
        $this->classToken = $tokens[$startIndex];
        $this->startBracketIndex = $tokens->getNextTokenOfKind($startIndex, ['{']);
        $this->endBracketIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $this->startBracketIndex);

        $this->tokens = $tokens;
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->startIndex = $startIndex;
    }

    public static function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): self
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$startIndex], [T_CLASS, T_INTERFACE, T_TRAIT], self::class);

        return new self($tokens, $startIndex);
    }

    /**
     * @return mixed[]
     */
    public function getPropertiesAndConstants(): array
    {
        return $this->filterClassyTokens($this->getClassyElements(), ['property', 'const']);
    }

    public function getClassEnd(): int
    {
        return $this->endBracketIndex;
    }

    /**
     * @return mixed[]
     */
    public function getProperties(): array
    {
        return $this->filterClassyTokens($this->getClassyElements(), ['property']);
    }

    public function getLastPropertyPosition(): ?int
    {
        $properties = $this->getProperties();
        if ($properties === []) {
            return null;
        }

        end($properties);

        return key($properties);
    }

    public function getFirstMethodPosition(): ?int
    {
        $methods = $this->getMethods();
        if ($methods === []) {
            return null;
        }

        end($methods);

        return key($methods);
    }

    /**
     * @return mixed[]
     */
    public function getMethods(): array
    {
        return $this->filterClassyTokens($this->getClassyElements(), ['method']);
    }

    public function renameEveryPropertyOccurrence(string $oldName, string $newName): void
    {
        for ($i = $this->startBracketIndex + 1; $i < $this->endBracketIndex; ++$i) {
            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            if ($token->getContent() !== '$this') {
                continue;
            }

            $propertyAccessWrapper = PropertyAccessWrapper::createFromTokensAndPosition($this->tokens, $i);

            if ($propertyAccessWrapper->getName() === $oldName) {
                $propertyAccessWrapper->changeName($newName);
            }
        }
    }

    /**
     * @return PropertyWrapper[]
     */
    public function getPropertyWrappers(): array
    {
        $propertyWrappers = [];

        foreach ($this->getProperties() as $propertyPosition => $propertyToken) {
            $propertyWrappers[] = PropertyWrapper::createFromTokensAndPosition($this->tokens, $propertyPosition);
        }

        return $propertyWrappers;
    }

    /**
     * @return MethodWrapper[]
     */
    public function getMethodWrappers(): array
    {
        $methodWrappers = [];

        foreach ($this->getMethods() as $methodPosition => $methodToken) {
            $methodWrappers[] = MethodWrapper::createFromTokensAndPosition($this->tokens, $methodPosition);
        }

        return $methodWrappers;
    }

    /**
     * @param int[] $tokenKinds
     */
    public function isGivenKind(array $tokenKinds): bool
    {
        return $this->classToken->isGivenKind($tokenKinds);
    }

    public function implementsInterface(): bool
    {
        return (bool) $this->getInterfaceNames();
    }

    public function isFinal(): bool
    {
        return (bool) $this->tokens->findGivenKind(T_FINAL, 0, $this->startIndex);
    }

    public function isAbstract(): bool
    {
        return (bool) $this->tokens->findGivenKind(T_ABSTRACT, 0, $this->startIndex);
    }

    public function isDoctrineEntity(): bool
    {
        $docCommentPosition = DocBlockFinder::findPreviousPosition($this->tokens, $this->startIndex);
        if (! $docCommentPosition) {
            return false;
        }

        return Strings::contains($this->tokens[$docCommentPosition]->getContent(), 'Entity');
    }

    /**
     * @return string[]
     */
    public function getInterfaceNames(): array
    {
        $implementTokens = $this->tokens->findGivenKind(T_IMPLEMENTS, $this->startIndex, $this->startBracketIndex);

        if (! $implementTokens) {
            return [];
        }

        reset($implementTokens);

        $implementPosition = key($implementTokens);

        $interfacePartialNameTokens = $this->tokens->findGivenKind(
            T_STRING,
            $implementPosition,
            $this->startBracketIndex
        );

        $interfaceNames = [];
        foreach ($interfacePartialNameTokens as $position => $interfacePartialNameToken) {
            $interfaceNames[] = NameFactory::createFromTokensAndStart($this->tokens, $position)->getName();
        }

        return $interfaceNames;
    }

    public function clearImplements(): void
    {
        $implementTokens = $this->tokens->findGivenKind(T_IMPLEMENTS, $this->startIndex, $this->startBracketIndex);

        reset($implementTokens);
        $implementPosition = key($implementTokens);

        $this->tokens->clearAt($implementPosition - 1);

        for ($i = $implementPosition; $i < $this->startBracketIndex; ++$i) {
            if (Strings::contains($this->tokens[$i]->getContent(), PHP_EOL)) {
                return;
            }

            $this->tokens->clearAt($i);
        }
    }

    /**
     * @param mixed[] $classyElements
     * @param string[] $types
     * @return mixed[]
     */
    private function filterClassyTokens(array $classyElements, array $types): array
    {
        $filteredClassyTokens = [];

        foreach ($classyElements as $index => $classyToken) {
            if (! $this->isInClassRange($index)) {
                continue;
            }

            if (! in_array($classyToken['type'], $types, true)) {
                continue;
            }

            $filteredClassyTokens[$index] = $classyToken;
        }

        return $filteredClassyTokens;
    }

    private function isInClassRange(int $index): bool
    {
        if ($index < $this->startBracketIndex) {
            return false;
        }

        if ($index > $this->endBracketIndex) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed[]
     */
    private function getClassyElements(): array
    {
        if ($this->classyElements) {
            return $this->classyElements;
        }

        return $this->classyElements = $this->tokensAnalyzer->getClassyElements();
    }
}
