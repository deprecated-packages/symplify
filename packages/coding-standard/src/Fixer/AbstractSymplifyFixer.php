<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ReflectionClass;
use SplFileInfo;

abstract class AbstractSymplifyFixer implements DefinedFixerInterface
{
    public function getPriority(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    /**
     * @return Token[]
     */
    protected function reverseTokens(Tokens $tokens): array
    {
        return array_reverse($tokens->toArray(), true);
    }

    /**
     * @return int[]
     */
    protected function getReversedClassyPositions(Tokens $tokens): array
    {
        $classyTokensByTokenKind = $tokens->findGivenKind(Token::getClassyTokenKinds());
        return $this->getReversedPositionsFromTokens($classyTokensByTokenKind);
    }

    /**
     * @return int[]
     */
    protected function getReversedClassAndTraitPositions(Tokens $tokens): array
    {
        $classyTokensByTokenKind = $tokens->findGivenKind([T_CLASS, T_TRAIT]);
        return $this->getReversedPositionsFromTokens($classyTokensByTokenKind);
    }

    /**
     * Helper method to run this before specified fixer,
     * works even in case of change.
     */
    protected function getPriorityBefore(string $fixerClass): int
    {
        if (! is_a($fixerClass, FixerInterface::class, true)) {
            return 0;
        }

        /** @var FixerInterface $fixer */
        $fixer = (new ReflectionClass($fixerClass))->newInstanceWithoutConstructor();

        return $fixer->getPriority() + 5;
    }

    /**
     * @param Token[] $tokens
     * @return int[]
     */
    private function getReversedPositionsFromTokens(array $tokens): array
    {
        $classyTokens = array_replace(...$tokens);

        return array_reverse(array_keys($classyTokens));
    }
}
