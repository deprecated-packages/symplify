<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenAnalyzer;

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Copied from \PhpCsFixer\AbstractDoctrineAnnotationFixer::nextElementAcceptsDoctrineAnnotations() so it can be used as
 * a normal service
 */
final class DoctrineAnnotationElementAnalyzer
{
    /**
     * We look for "(@SomeAnnotation"
     *
     * @param DoctrineAnnotationTokens<Token> $doctrineAnnotationTokens
     */
    public function isOpeningBracketFollowedByAnnotation(
        Token $token,
        DoctrineAnnotationTokens $doctrineAnnotationTokens,
        int $braceIndex
    ): bool {
        // should be "("
        $isNextOpenParenthesis = $token->isType(DocLexer::T_OPEN_PARENTHESIS);
        if (! $isNextOpenParenthesis) {
            return false;
        }

        $nextTokenIndex = $doctrineAnnotationTokens->getNextMeaningfulToken($braceIndex);
        if ($nextTokenIndex === null) {
            return false;
        }

        /** @var Token $nextToken */
        $nextToken = $doctrineAnnotationTokens[$nextTokenIndex];

        // next token must be nested annotation, we don't care otherwise
        return $nextToken->isType(DocLexer::T_AT);
    }
}
