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
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\DoctrineAnnotationNewlineInNestedAnnotationFixerTest
 */
final class DoctrineAnnotationNewlineInNestedAnnotationFixer extends AbstractDoctrineAnnotationFixer
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
     * @param iterable<Token>&Tokens $tokens
     */
    protected function fixAnnotations(Tokens $tokens): void
    {
        $this->currentBlockInfo = null;

        $tokenCount = $tokens->count();

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

            if ($this->shouldSkip($index, $tokens, $previousToken)) {
                continue;
            }

            $tokens->insertAt($index, new Token(DocLexer::T_NONE, ' * '));
            $tokens->insertAt($index, new Token(DocLexer::T_NONE, "\n"));
            $tNone = $previousToken->isType(DocLexer::T_NONE);

            // remove redundant space
            if ($tNone) {
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

    private function processEndBracket(int $index, Tokens $tokens, int $previousTokenPosition): void
    {
        /** @var Token $previousToken */
        $previousToken = $tokens->offsetGet($previousTokenPosition);
        // already a space → skip
        if ($previousToken->isType(DocLexer::T_NONE)) {
            return;
        }

        // reset
        if ($this->currentBlockInfo !== null && ! $this->currentBlockInfo->contains($index)) {
            $this->currentBlockInfo = null;
            return;
        }

        if ($this->currentBlockInfo === null) {
            $this->currentBlockInfo = $this->doctrineBlockFinder->findInTokensByEdge(
                $tokens,
                $previousTokenPosition
            );
        }

        if ($this->currentBlockInfo !== null) {
            $tokens->insertAt($this->currentBlockInfo->getEnd(), new Token(DocLexer::T_NONE, ' * '));
            $tokens->insertAt($this->currentBlockInfo->getEnd(), new Token(DocLexer::T_NONE, "\n"));
        }
    }

    private function shouldSkip(int $index, Tokens $tokens, Token $previousToken): bool
    {
        // docblock opener → skip it
        if ($this->isDocOpener($previousToken)) {
            return true;
        }

        $nextTokenPosition = $index + 1;

        /** @var Token|null $nextToken */
        $nextToken = $tokens[$nextTokenPosition] ?? null;
        if ($nextToken === null) {
            return true;
        }

        if (! Strings::startsWith($nextToken->getContent(), 'ORM')) {
            return true;
        }

        // not an entity annotation, just some comment
        return $nextToken->getContent() === 'ORM';
    }
}
