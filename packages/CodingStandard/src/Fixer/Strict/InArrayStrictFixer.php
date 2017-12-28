<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Strict;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Thanks for inspiration to https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/490/files
 */
final class InArrayStrictFixer implements FixerInterface, DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'in_array() should use 3rd param for strict comparison',
            [new CodeSample('<?php in_array("key", []);')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_STRING, '(', ')', ',']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[]|Tokens $tokensReversed */
        $tokensReversed = array_reverse($tokens->toArray(), true);

        foreach ($tokensReversed as $position => $token) {
            // look for "in_array"
            if ($token->getId() !== T_STRING || $token->getContent() !== 'in_array') {
                continue;
            }

            $openBracketPosition = $position + 1;
            $closeBracketPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBracketPosition);

            // get last token
            $lastTokenPosition = $tokens->getPrevNonWhitespace($closeBracketPosition);
            $lastToken = $tokens[$lastTokenPosition];

            if (in_array(strtolower($lastToken->getContent()), ['true', 'false'], true)) {
                continue;
            }

            $tokens->insertAt($lastTokenPosition + 1, [
                new Token(','),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, 'true']),
            ]);
        }
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function isRisky(): bool
    {
        return false;
    }
}
