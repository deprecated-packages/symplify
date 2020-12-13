<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\LineLengthFixerTest
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\ConfiguredLineLengthFixerTest
 */
final class LineLengthFixer extends AbstractSymplifyFixer implements ConfigurableRuleInterface, DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    public const LINE_LENGTH = 'line_length';

    /**
     * @api
     * @var string
     */
    public const BREAK_LONG_LINES = 'break_long_lines';

    /**
     * @api
     * @var string
     */
    public const INLINE_SHORT_LINES = 'inline_short_lines';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.';

    /**
     * @var int
     */
    private $lineLength = 120;

    /**
     * @var bool
     */
    private $breakLongLines = true;

    /**
     * @var bool
     */
    private $inlineShortLines = true;

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(LineLengthTransformer $lineLengthTransformer, BlockFinder $blockFinder)
    {
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockFinder = $blockFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            // "["
            T_ARRAY,
            // "array"()
            CT::T_ARRAY_SQUARE_BRACE_OPEN,
            '(',
            ')',
            // "function"
            T_FUNCTION,
            // "use" (...)
            CT::T_USE_LAMBDA,
            // "new"
            T_NEW,
        ]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];

            if ($token->equals(')')) {
                $this->processMethodCall($tokens, $position);
                continue;
            }

            // opener
            if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
                $this->processFunctionOrArray($tokens, $position);
                continue;
            }

            // closer
            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) || ($token->equals(')') && $token->isArray())) {
                $this->processFunctionOrArray($tokens, $position);
            }
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(TrimArraySpacesFixer::class);
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->lineLength = $configuration[self::LINE_LENGTH] ?? 120;
        $this->breakLongLines = $configuration[self::BREAK_LONG_LINES] ?? true;
        $this->inlineShortLines = $configuration[self::INLINE_SHORT_LINES] ?? true;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
function some($veryLong, $superLong, $oneMoreTime)
{
}

function another(
    $short,
    $now
) {
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function some(
    $veryLong,
    $superLong,
    $oneMoreTime
) {
}

function another($short, $now) {
}
CODE_SAMPLE
                ,
                [
                    self::LINE_LENGTH => 40,
                ]
            ),
        ]);
    }

    private function processMethodCall(Tokens $tokens, int $position): void
    {
        $methodNamePosition = $this->matchNamePositionForEndOfFunctionCall($tokens, $position);
        if ($methodNamePosition === null) {
            return;
        }

        $blockInfo = $this->blockFinder->findInTokensByPositionAndContent($tokens, $methodNamePosition, '(');
        if ($blockInfo === null) {
            return;
        }

        // has comments => dangerous to change: https://github.com/symplify/symplify/issues/973
        $comments = $tokens->findGivenKind(T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd());
        if ($comments !== []) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition(
            $blockInfo,
            $tokens,
            $this->lineLength,
            $this->breakLongLines,
            $this->inlineShortLines
        );
    }

    private function processFunctionOrArray(Tokens $tokens, int $position): void
    {
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if ($blockInfo === null) {
            return;
        }

        if ($this->shouldSkip($tokens, $blockInfo)) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition(
            $blockInfo,
            $tokens,
            $this->lineLength,
            $this->breakLongLines,
            $this->inlineShortLines
        );
    }

    /**
     * We go through tokens from down to up,
     * so we need to find ")" and then the start of function
     */
    private function matchNamePositionForEndOfFunctionCall(Tokens $tokens, int $position): ?int
    {
        try {
            $blockStart = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $position);
        } catch (Throwable $throwable) {
            // not a block start
            return null;
        }

        $previousTokenPosition = $blockStart - 1;
        /** @var Token $possibleMethodNameToken */
        $possibleMethodNameToken = $tokens[$previousTokenPosition];

        // not a "methodCall()"
        if (! $possibleMethodNameToken->isGivenKind(T_STRING)) {
            return null;
        }

        // starts with small letter?
        $content = $possibleMethodNameToken->getContent();
        if (! ctype_lower($content[0])) {
            return null;
        }

        // is "someCall()"? we don't care, there are no arguments
        if ($tokens[$blockStart + 1]->equals(')')) {
            return null;
        }

        return $previousTokenPosition;
    }

    private function shouldSkip(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        // no items inside => skip
        if ($blockInfo->getEnd() - $blockInfo->getStart() <= 1) {
            return true;
        }

        // heredoc/nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($blockInfo->getStart());
        /** @var Token $nextToken */
        $nextToken = $tokens[$nextTokenPosition];

        if (Strings::contains($nextToken->getContent(), '<<<')) {
            return true;
        }

        // is array with indexed values "=>"
        $doubleArrowTokens = $tokens->findGivenKind(T_DOUBLE_ARROW, $blockInfo->getStart(), $blockInfo->getEnd());
        if ($doubleArrowTokens !== []) {
            return true;
        }

        // has comments => dangerous to change: https://github.com/symplify/symplify/issues/973
        return (bool) $tokens->findGivenKind(T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd());
    }
}
