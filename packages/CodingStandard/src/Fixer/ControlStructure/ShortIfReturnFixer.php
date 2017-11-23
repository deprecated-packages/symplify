<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ControlStructure;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;
use UnexpectedValueException;

final class ShortIfReturnFixer implements DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Short if return should be used.',
            [
                new CodeSample('<?php
if ($value === true) {
    return true;
}
return false;'),
            ]
        );
    }

    /**
     * Check if the fixer is a candidate for given Tokens collection.
     *
     * Fixer is a candidate when the collection contains tokens that may be fixed
     * during fixer work. This could be considered as some kind of bloom filter.
     * When this method returns true then to the Tokens collection may or may not
     * need a fixing, but when this method returns false then the Tokens collection
     * need no fixing for sure.
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_IF, T_RETURN, T_IS_IDENTICAL]);
    }

    /**
     * Check if fixer is risky or not.
     *
     * Risky fixer could change code behavior!
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * Fixes a file.
     *
     * @param \SplFileInfo $file A \SplFileInfo instance
     * @param Tokens $tokens Tokens collection
     */
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_IF)) {
                continue;
            }

            $nextTokenId = $tokens->getNextNonWhitespace($index);
            $nextToken = $tokens[$nextTokenId];

            // Try to look for condition surrounded by braces, mark its start and end position
            if ($nextToken->getContent() !== '(') {
                continue;
            }

            $conditionStart = $nextTokenId;

            try {
                $conditionEnd = $this->getConditionEnd($tokens, $conditionStart);
            } catch (UnexpectedValueException $e) {
                continue;
            }

            // Get condition body end - it must consist only of "return true;"
            $tokenId = $tokens->getNextNonWhitespace($conditionEnd);

            try {
                $tokenId = $this->getConditionBodyEnd($tokens, $tokenId);
            } catch (UnexpectedTokenException $e) {
                continue;
            }

            // Get statement after condition end - must be "return false;"
            try {
                $tokenId = $this->getStatementAfterConditionEnd($tokens, $tokenId);
            } catch (UnexpectedTokenException $e) {
                continue;
            }

            $lastTokenId = $tokenId;
            $shortReturnTokens = $this->createShortReturnTokens($tokens, $conditionStart, $conditionEnd);
            $tokenId = $index;
            while ($tokenId !== $lastTokenId) {
                $tokens->clearAt($tokenId);
                ++$tokenId;
            }

            $tokens->clearAt($tokenId);
            $tokens->clearEmptyTokens();
            $tokens->insertAt($index, $shortReturnTokens);
        }
    }

    /**
     * Returns the name of the fixer.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the fixer
     */
    public function getName(): string
    {
        return self::class;
    }

    /**
     * Returns the priority of the fixer.
     *
     * The default priority is 0 and higher priorities are executed first.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * Returns true if the file is supported by this fixer.
     *
     *
     * @return bool true if the file is supported by this fixer, false otherwise
     */
    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function getConditionEnd(Tokens $tokens, int $conditionStart): int
    {
        return $tokens->findBlockEnd($tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $conditionStart);
    }

    private function getConditionBodyEnd(Tokens $tokens, int $tokenId): int
    {
        if ($tokens[$tokenId]->getContent() !== '{') {
            throw new UnexpectedTokenException;
        }

        $tokenId = $tokens->getNextNonWhitespace($tokenId);

        if (! $tokens[$tokenId]->isGivenKind(T_RETURN)) {
            throw new UnexpectedTokenException;
        }

        $tokenId = $tokens->getNextNonWhitespace($tokenId);
        if (strtolower($tokens[$tokenId]->getContent()) !== 'true') {
            throw new UnexpectedTokenException;
        }

        $tokenId = $tokens->getNextNonWhitespace($tokenId);
        if ($tokens[$tokenId]->getContent() !== ';') {
            throw new UnexpectedTokenException;
        }

        $tokenId = $tokens->getNextNonWhitespace($tokenId);
        if ($tokens[$tokenId]->getContent() !== '}') {
            throw new UnexpectedTokenException;
        }

        return $tokenId;
    }

    private function getStatementAfterConditionEnd(Tokens $tokens, int $tokenId): int
    {
        $tokenId = $tokens->getNextNonWhitespace($tokenId);
        if (! $tokens[$tokenId]->isGivenKind(T_RETURN)) {
            throw new UnexpectedTokenException;
        }

        $tokenId = $tokens->getNextNonWhitespace($tokenId);
        if (strtolower($tokens[$tokenId]->getContent()) !== 'false') {
            throw new UnexpectedTokenException;
        }

        $tokenId = $tokens->getNextNonWhitespace($tokenId);
        if ($tokens[$tokenId]->getContent() !== ';') {
            throw new UnexpectedTokenException;
        }

        return $tokenId;
    }

    /**
     * @return Token[]
     */
    private function createShortReturnTokens(Tokens $tokens, int $conditionStart, int $conditionEnd): array
    {
        $shortReturnTokens = [
            new Token([T_RETURN, 'return']),
            new Token([T_WHITESPACE, ' ']),
        ];
        $shortReturnTokens = array_merge(
            $shortReturnTokens,
            $this->getConditionNonEmptyTokens($tokens, $conditionStart, $conditionEnd)
        );
        $shortReturnTokens[] = new Token(';');

        return $shortReturnTokens;
    }

    /**
     * @return Token[]
     */
    private function getConditionNonEmptyTokens(Tokens $tokens, int $conditionStart, int $conditionEnd): array
    {
        $conditionTokens = [];
        $tokenId = $tokens->getNonEmptySibling($conditionStart, 1);
        while ($tokenId !== $conditionEnd) {
            $conditionTokens[] = $tokens[$tokenId];
            $tokenId = $tokens->getNonEmptySibling($tokenId, 1);
        }

        return $conditionTokens;
    }
}
