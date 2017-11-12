<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class RemoveEmptyDocBlockFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There should not be empty PHPDoc blocks.',
            [new CodeSample('<?php 

/**  */
')]
        );
    }

    /**
     * Should be run after PhpdocNoEmptyReturnFixer.
     */
    public function getPriority(): int
    {
        return 5;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens); $index > 0; --$index) {
            if (! isset($tokens[$index])) {
                continue;
            }

            $token = $tokens[$index];
            if (! $token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            if (! preg_match('#^/\*\*[\s\*]*\*/$#', $token->getContent())) {
                continue;
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            $previousToken = $tokens[$index - 1];
            if ($previousToken->isWhitespace()) {
                $previousWhitespaceContent = $previousToken->getContent();

                $lastLineBreak = strrpos($previousWhitespaceContent, PHP_EOL);
                $newWhitespaceContent = substr($previousWhitespaceContent, 0, $lastLineBreak);
                if ($newWhitespaceContent) {
                    $tokens[$index - 1] = new Token([T_WHITESPACE, $newWhitespaceContent]);
                } else {
                    $tokens->clearAt($index - 1);
                }
            }
        }
    }
}
