<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

final class RemoveEndOfFunctionCommentFixer extends AbstractSymplifyFixer
{
    /**
     * @var string
     */
    public const END_OF_FUNCTION_PATTERN = '#// end (.*?)#';

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;

        trigger_error(sprintf('Fixer "%s" is deprecated. Use regular expression instead', self::class));

        sleep(3);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Old "// end of function" comments should be removed', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, T_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if (! Strings::match($token->getContent(), self::END_OF_FUNCTION_PATTERN)) {
                continue;
            }

            $previousMeaningfulPosition = $tokens->getTokenNotOfKindSibling($index, -1, [[T_WHITESPACE]]);
            if ($previousMeaningfulPosition === null) {
                continue;
            }

            // right after the end of functions
            if ($tokens[$previousMeaningfulPosition]->getContent() !== '}') {
                continue;
            }

            $tokens->clearAt($index);

            $previousToken = $tokens[$index - 1];
            $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

            if ($previousToken->isWhitespace() && ! Strings::contains($previousToken->getContent(), $lineEnding)) {
                $tokens->clearAt($index - 1);
                --$index;
            }
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(ClassDefinitionFixer::class);
    }
}
