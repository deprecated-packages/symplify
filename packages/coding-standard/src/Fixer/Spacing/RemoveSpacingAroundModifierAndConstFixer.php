<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class RemoveSpacingAroundModifierAndConstFixer implements FixerInterface
{
    /**
     * @var int[]
     */
    private const MODIFIER_TOKENS = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_CONST];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Remove extra around public/protected/private/static modifiers and const', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::MODIFIER_TOKENS);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token $token */
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(self::MODIFIER_TOKENS)) {
                continue;
            }

            $nextTokenPosition = $index + 1;
            if (! isset($tokens[$nextTokenPosition])) {
                continue;
            }

            /** @var Token $nextToken */
            $nextToken = $tokens[$nextTokenPosition];
            if (! $nextToken->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            // already one space → skip
            if ($nextToken->getContent() === ' ') {
                continue;
            }

            // use just one space
            $tokens[$nextTokenPosition] = new Token([T_WHITESPACE, ' ']);
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

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $splFileInfo): bool
    {
        return true;
    }
}
