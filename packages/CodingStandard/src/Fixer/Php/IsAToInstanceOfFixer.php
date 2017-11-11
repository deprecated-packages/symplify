<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Php;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use SplFileInfo;

/**
 * @author Martin Patera <mzstic@gmail.com>
 */
final class IsAToInstanceOfFixer implements DefinedFixerInterface
{
    /**
     * Returns the definition of the fixer.
     *
     * @return FixerDefinitionInterface
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Prefer instance of over is_a()',
            [
                new CodeSample('<?php
if (is_a($var, $type, true))'),
                new CodeSample('<?php
if (is_a($var, ClassName::class, true))'),
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
     *
     *
     * @return bool
     */
    public function isCandidate(Tokens $tokens): bool
    {
        $anyFunction = $tokens->isAnyTokenKindsFound([T_STRING]);
        if (! $anyFunction) {
            return false;
        }

        $tokenId = 0;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_STRING)) {
                if ($token->getContent() === 'is_a') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if fixer is risky or not.
     *
     * Risky fixer could change code behavior!
     *
     * @return bool
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
            if (! $token->isGivenKind(T_STRING)) {
                continue;
            }

            if ($token->getContent() !== 'is_a') {
                continue;
            }

            $isAStart = $index;
            $tokenId = $index;
            while ($tokens[$tokenId]->getContent() !== ')') {
                $tokenId = $tokens->getNextNonWhitespace($tokenId);
            }

            $isAEnd = $tokenId;

            $firstComma = $this->findFirstComma($tokens, $tokenId);

            $secondComma = $this->findSecondComma($tokens, $tokenId);

            $instanceOfTokens = $this->createInstanceOfTokens($tokens, $isAStart, $firstComma, $secondComma);

            $this->replaceTokens($tokens, $isAStart, $isAEnd, $instanceOfTokens);
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
     *
     * @return int
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

    private function findFirstComma(Tokens $tokens, $tokenId): void
    {
        // @TODO implement
    }

    private function findSecondComma(Tokens $tokens, $tokenId): void
    {
        // @TODO implement
    }

    private function createInstanceOfTokens($tokens, $isAStart, $firstComma, $secondComma): void
    {
        // @TODO implement
    }

    private function replaceTokens(Tokens $tokens, $isAStart, $isAEnd, $instanceOfTokens): void
    {
        // @TODO implement
    }
}
