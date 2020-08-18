<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Annotation;

use Doctrine\Common\Annotations\DocLexer;
use Nette\Utils\Strings;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\DoctrineBlockFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\NewlineInNestedAnnotationFixer\NewlineInNestedAnnotationFixerTest
 */
final class NewlineInNestedAnnotationFixer extends AbstractDoctrineAnnotationFixer
{
    /**
     * @var DoctrineBlockFinder
     */
    private $doctrineBlockFinder;

    /**
     * @var BlockInfo|null
     */
    private $currentBlockInfo;

    public function __construct(DoctrineBlockFinder $doctrineBlockFinder)
    {
        $this->doctrineBlockFinder = $doctrineBlockFinder;

        parent::__construct();
    }

    public function getPriority(): int
    {
        // must run before \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer
        return 100;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Nested annotation should start on standalone line', []);
    }

    /**
     * @note indent is covered by
     * @see \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer
     *
     * @param iterable<\PhpCsFixer\Doctrine\Annotation\Token>&Tokens $tokens
     */
    protected function fixAnnotations(Tokens $tokens): void
    {
        $tokenCount = $tokens->count();

        $this->currentBlockInfo = null;

        // what about foreach?
        for ($index = 0; $index < $tokenCount; ++$index) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$index];
            if (! $currentToken->isType(DocLexer::T_AT)) {
                continue;
            }

            /** @var Token|null $previousToken */
            $previousTokenPosition = $index - 1;
            $previousToken = $tokens[$previousTokenPosition] ?? null;
            if ($previousToken === null) {
                continue;
            }

            // docblock opener → skip it
            if ($this->isDocOpener($previousToken)) {
                continue;
            }

            $tokens->insertAt($index, new Token(DocLexer::T_NONE, ' * '));
            $tokens->insertAt($index, new Token(DocLexer::T_NONE, "\n"));

            // remove redundant space
            if ($previousToken->isType(DocLexer::T_NONE)) {
                $tokens->offsetUnset($previousTokenPosition);
            }

            $this->processEndBracket($index, $tokens, $previousTokenPosition);
        }
    }

    private function isDocOpener(Token $token): bool
    {
        if ($token->isType(DocLexer::T_NONE)) {
            return Strings::contains($token->getContent(), '*');
        }

        return false;
    }

    private function processEndBracket(int $index, $tokens, int $previousTokenPosition): void
    {
        if ($this->currentBlockInfo === null || ! $this->currentBlockInfo->contains($index)) {
            $this->currentBlockInfo = $this->doctrineBlockFinder->findInTokensByEdge(
                $tokens,
                $previousTokenPosition
            );

            if ($this->currentBlockInfo !== null) {
                $tokens->insertAt($this->currentBlockInfo->getEnd(), new Token(DocLexer::T_NONE, ' * '));
                $tokens->insertAt($this->currentBlockInfo->getEnd(), new Token(DocLexer::T_NONE, "\n"));
            }
        }
    }
}
