<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ControlStructure;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class RequireFollowedByAbsolutePathFixer implements DefinedFixerInterface
{
    /**
     * @var int[]
     */
    private const INCLUDY_TOKEN_KINDS = [T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Include/Require should be followed by absolute path.',
            [
                new CodeSample('require "vendor/autoload.php"'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::INCLUDY_TOKEN_KINDS);
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(self::INCLUDY_TOKEN_KINDS)) {
                continue;
            }

            $nextTokenPosition = $tokens->getNextNonWhitespace($index);
            $nextToken = $tokens[$nextTokenPosition];

            if (! $nextToken->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $tokens->insertAt($nextTokenPosition, [
                new Token([T_DIR, '__DIR__']),
                new Token('.'),
            ]);
        }
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Must run before @see ConcatSpaceFixer.
     */
    public function getPriority(): int
    {
        return 5;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }
}
