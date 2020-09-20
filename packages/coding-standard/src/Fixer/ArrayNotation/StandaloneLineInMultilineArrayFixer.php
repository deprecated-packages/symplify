<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\StandaloneLineInMultilineArrayFixerTest
 */
final class StandaloneLineInMultilineArrayFixer extends AbstractArrayFixer
{
    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var ArrayWrapperFactory
     */
    private $arrayWrapperFactory;

    public function __construct(LineLengthTransformer $lineLengthTransformer, ArrayWrapperFactory $arrayWrapperFactory)
    {
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->arrayWrapperFactory = $arrayWrapperFactory;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Indexed PHP arrays should have 1 item per line.', []);
    }

    public function fixArrayOpener(Tokens $tokens, BlockInfo $blockInfo, int $index): void
    {
        if ($this->shouldSkip($tokens, $blockInfo)) {
            return;
        }

        $this->lineLengthTransformer->breakItems($blockInfo, $tokens);
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(TrailingCommaInMultilineArrayFixer::class);
    }

    /**
     * skip: [$array => value]
     * keep: [$array => [value => nested]]
     */
    private function shouldSkip(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        $arrayWrapper = $this->arrayWrapperFactory->createFromTokensAndBlockInfo($tokens, $blockInfo);
        if (! $arrayWrapper->isAssociativeArray()) {
            return true;
        }

        if ($arrayWrapper->getItemCount() === 1 && ! $arrayWrapper->isFirstItemArray()) {
            $previousTokenPosition = $tokens->getPrevMeaningfulToken($blockInfo->getStart());
            if ($previousTokenPosition === null) {
                return false;
            }

            /** @var Token $previousToken */
            $previousToken = $tokens[$previousTokenPosition];
            return ! $previousToken->isGivenKind(T_DOUBLE_ARROW);
        }

        return false;
    }
}
