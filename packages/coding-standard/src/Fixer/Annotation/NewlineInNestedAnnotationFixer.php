<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Annotation;

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use Symplify\CodingStandard\Exception\NotImplementedYetException;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\DoctrineBlockFinder;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\NewlineInNestedAnnotationFixer\NewlineInNestedAnnotationFixerTest
 */
final class NewlineInNestedAnnotationFixer extends AbstractDoctrineAnnotationFixer
{
    /**
     * @var int
     */
    private $indentCounter = 0;

    /**
     * @var DoctrineBlockFinder
     */
    private $doctrineBlockFinder;

    public function __construct(DoctrineBlockFinder $doctrineBlockFinder)
    {
        $this->doctrineBlockFinder = $doctrineBlockFinder;

        parent::__construct();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Nested annotation should start on standalone line', []);
    }

    /**
     * @param iterable<\PhpCsFixer\Doctrine\Annotation\Token>&Tokens $tokens
     */
    protected function fixAnnotations(Tokens $tokens): void
    {
        $tokenCount = $tokens->count();

        $this->indentCounter = 0;

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

            ++$this->indentCounter;

            // docblock opener → skip it
            if ($previousToken->isType(DocLexer::T_NONE)) {
                continue;
            }

            if (! $previousToken->isType(DocLexer::T_OPEN_CURLY_BRACES)) {
                throw new NotImplementedYetException();
            }

            // add a newline with indent - @todo resolve indent and removing of spaces
            $indentWhitespace = str_repeat(' ', ($this->indentCounter - 1) * 4);
            $tokens->insertAt($index, new Token(DocLexer::T_NONE, $indentWhitespace));
            $tokens->insertAt($index, new Token(DocLexer::T_NONE, ' * '));
            $tokens->insertAt($index, new Token(DocLexer::T_NONE, "\n"));

            $block = $this->doctrineBlockFinder->findInTokensByEdge($tokens, $previousTokenPosition);
            if ($block !== null) {
                $tokens->insertAt($block->getEnd(), new Token(DocLexer::T_NONE, ' * '));
                $tokens->insertAt($block->getEnd(), new Token(DocLexer::T_NONE, "\n"));
            }
        }
    }
}
