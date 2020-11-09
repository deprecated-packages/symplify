<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Fixer\AbstractArrayFixer;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineKind;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\StandaloneLineInMultilineArrayFixerTest
 */
final class StandaloneLineInMultilineArrayFixer extends AbstractArrayFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Indexed arrays must have 1 item per line';

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
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function fixArrayOpener(Tokens $tokens, BlockInfo $blockInfo, int $index): void
    {
        if ($this->shouldSkip($tokens, $blockInfo)) {
            return;
        }

        $this->lineLengthTransformer->breakItems($blockInfo, $tokens, LineKind::ARRAYS);
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(TrailingCommaInMultilineArrayFixer::class);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$friends = [1 => 'Peter', 2 => 'Paul'];
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$friends = [
    1 => 'Peter',
    2 => 'Paul'
];
CODE_SAMPLE
            ),
        ]);
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
