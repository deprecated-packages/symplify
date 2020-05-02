<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symplify\CodingStandard\TokenRunner\Naming\Name\NameFactory;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Types\ClassLikeExistenceChecker;

final class FixerClassWrapper
{
    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var string|null
     */
    private $className;

    /**
     * @var int
     */
    private $startBracketIndex;

    /**
     * @var int
     */
    private $endBracketIndex;

    /**
     * @var string[]
     */
    private $classTypes = [];

    /**
     * @var mixed[]
     *
     * Rich information about methods, e.g.:
     *
     *  0 => array (6)
     *     |  start => 18
     *     |  visibility => "public" (6)
     *     |  static => FALSE
     *     |  type => "method" (6)
     *     |  name => "secondMethod" (12)
     *     |  end => 29
     */
    private $methodElements = [];

    /**
     * @var mixed[]
     */
    private $propertyElements = [];

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    public function __construct(
        Tokens $tokens,
        int $startIndex,
        NameFactory $nameFactory,
        ClassLikeExistenceChecker $classLikeExistenceChecker
    ) {
        $this->startBracketIndex = $tokens->getNextTokenOfKind($startIndex, ['{']);

        if ($this->startBracketIndex !== null) {
            $this->endBracketIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $this->startBracketIndex);
        }

        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->nameFactory = $nameFactory;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }

    public function getClassName(): ?string
    {
        if ($this->className) {
            return $this->className;
        }

        $namePosition = $this->getNamePosition();
        if ($namePosition === null) {
            return null;
        }

        $className = $this->nameFactory->createFromTokensAndStart($this->tokens, $namePosition);

        return $this->className = $className->getName();
    }

    public function getParentClassName(): ?string
    {
        $extendsTokens = $this->tokens->findGivenKind(T_EXTENDS, $this->startIndex);
        if ($extendsTokens === []) {
            return null;
        }

        $extendsPosition = $this->getArrayFirstKey($extendsTokens);

        /** @var Token[] $stringTokens */
        $stringTokens = $this->tokens->findGivenKind(T_STRING, $extendsPosition, $this->startBracketIndex);
        if (count($stringTokens) === 0) {
            return null;
        }

        $parentClassNamePosition = (int) key($stringTokens);
        $parentClassName = $this->nameFactory->createFromTokensAndStart($this->tokens, $parentClassNamePosition);

        return $parentClassName->getName();
    }

    public function isAbstract(): bool
    {
        return (bool) $this->tokens->findGivenKind(T_ABSTRACT, 0, $this->startIndex);
    }

    /**
     * @return mixed[]
     */
    public function getPropertyElements(): array
    {
        if ($this->propertyElements !== []) {
            return $this->propertyElements;
        }

        return $this->propertyElements = $this->getElementsByType('property');
    }

    /**
     * @return mixed[]
     */
    public function getMethodElements(): array
    {
        if ($this->methodElements !== []) {
            return $this->methodElements;
        }

        return $this->methodElements = $this->getElementsByType('method');
    }

    /**
     * @return mixed[]
     */
    public function getPrivateMethodElements(): array
    {
        return array_filter($this->getMethodElements(), function (array $element): bool {
            return $element['visibility'] === 'private';
        });
    }

    /**
     * @return string[]
     */
    public function getClassTypes(): array
    {
        if ($this->classTypes !== []) {
            return $this->classTypes;
        }

        // we can't handle anonymous classes
        if ($this->getClassName() === null) {
            return [];
        }

        // class it not autoloaded, so we can't give more types than just a name
        if (! $this->classLikeExistenceChecker->exists($this->getClassName())) {
            return [$this->getClassName()];
        }

        $classTypes = array_merge(
            [$this->getClassName()],
            class_parents($this->getClassName()),
            class_implements($this->getClassName())
        );

        // unique + reindex from 0
        return $this->classTypes = array_values(array_unique($classTypes));
    }

    /**
     * @return string[]
     */
    public function getThisMethodCallsByOrderOfAppearance(): array
    {
        $methodCalls = [];
        for ($i = $this->startBracketIndex; $i < $this->endBracketIndex; ++$i) {
            if (! $this->isThisMethodCall($this->tokens, $i)) {
                continue;
            }
            $token = $this->tokens[$i];
            $methodCalls[] = $token->getContent();
        }

        return array_unique($methodCalls);
    }

    private function getNamePosition(): ?int
    {
        if ((new TokensAnalyzer($this->tokens))->isAnonymousClass($this->startIndex)) {
            return null;
        }

        $stringTokens = $this->tokens->findGivenKind(T_STRING, $this->startIndex);
        if (count($stringTokens) === 0) {
            return null;
        }

        return $this->getArrayFirstKey($stringTokens);
    }

    /**
     * @param mixed[] $items
     */
    private function getArrayFirstKey(array $items): int
    {
        reset($items);
        return (int) key($items);
    }

    /**
     * @return mixed[]
     */
    private function getElementsByType(string $type): array
    {
        $elements = (new PrivatesCaller())->callPrivateMethod(
            new OrderedClassElementsFixer(),
            'getElements',
            $this->tokens,
            $this->startBracketIndex
        );

        $methodElements = array_filter($elements, function (array $element) use ($type): bool {
            return $element['type'] === $type;
        });

        // re-index from 0
        return array_values($methodElements);
    }

    private function isThisMethodCall(Tokens $tokens, int $index): bool
    {
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (! is_int($prevIndex)) {
            return false;
        }

        if (! $tokens[$prevIndex]->equals([T_OBJECT_OPERATOR, '->'])) {
            return false;
        }

        $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);

        /** @var Token $previousToken */
        $previousToken = $tokens[$prevPrevIndex];
        if ($previousToken->getContent() !== '$this') {
            return false;
        }

        return $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(');
    }
}
