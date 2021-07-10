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
     * @param Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function detect(Tokens $tokens, int $index): bool
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $classyElements = $tokensAnalyzer->getClassyElements();

        do {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($index === null) {
                return false;
            }
        } while ($tokens[$index]->isGivenKind([T_ABSTRACT, T_FINAL]));

        if ($tokens[$index]->isClassy()) {
            return true;
        }

        while ($tokens[$index]->isGivenKind(
            [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_NS_SEPARATOR, T_STRING, CT::T_NULLABLE_TYPE]
        )) {
            $index = $tokens->getNextMeaningfulToken($index);
            if (! is_int($index)) {
                return false;
            }
        }

        return isset($classyElements[$index]);
    }

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
