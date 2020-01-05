<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

final class RemoveSuperfluousDocBlockWhitespaceFixer extends AbstractSymplifyFixer
{
    /**
     * @var string
     */
    private const EMPTY_LINE_PATTERN = '#(?<oneLine>[\t ]+\*\n){2,}#';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Block comment should not have 2 empty lines in a row.', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $newContent = Strings::replace(
                $token->getContent(),
                self::EMPTY_LINE_PATTERN,
                function (array $match): string {
                    return $match['oneLine'];
                }
            );

            $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(NoEmptyCommentFixer::class);
    }
}
