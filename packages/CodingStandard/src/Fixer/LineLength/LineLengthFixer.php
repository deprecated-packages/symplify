<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockInfo;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Throwable;

final class LineLengthFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private const LINE_LENGHT_OPTION = 'line_length';

    /**
     * @var string
     */
    private const BREAK_LONG_LINES_OPTION = 'break_long_lines';

    /**
     * @var string
     */
    private const INLINE_SHORT_LINES_OPTION = 'inline_short_lines';

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    /**
     * @var mixed[]
     */
    private $configuration = [];

    public function __construct(LineLengthTransformer $lineLengthTransformer, BlockFinder $blockFinder)
    {
        // defaults
        $this->configure([]);

        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockFinder = $blockFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.',
            [
                new CodeSample(
                    '<?php
$array = ["loooooooooooooooooooooooooooooooongArraaaaaaaaaaay", "looooooooooooooooooooooooooooooooongArraaaaaaaaaaay"];'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            // "["
            T_ARRAY,
            // "array"();
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
            $token = $tokens[$position];

            if ($token->equals(')')) {
                $this->processMethodCall($tokens, $position);
                continue;
            }

            if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
                $this->processFunctionOrArray($tokens, $position);
                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) || ($token->equals(')') && $token->isArray())) {
                $this->processFunctionOrArray($tokens, $position);
                continue;
            }
        }
    }

    /**
     * Execute before @see \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer::getPriority()
     */
    public function getPriority(): int
    {
        return 5;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        if ($configuration === null) {
            return;
        }

        $this->configuration = $this->getConfigurationDefinition()
            ->resolve($configuration);
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $options = [];
        $options[] = (new FixerOptionBuilder(self::LINE_LENGHT_OPTION, 'Limit of line length.'))
            ->setAllowedTypes(['int'])
            ->setDefault(120)
            ->getOption();

        $options[] = (new FixerOptionBuilder(self::BREAK_LONG_LINES_OPTION, ' Should break long lines.'))
            ->setAllowedValues([true, false])
            ->setDefault(true)
            ->getOption();

        $options[] = (new FixerOptionBuilder(self::INLINE_SHORT_LINES_OPTION, ' Should inline short lines.'))
            ->setAllowedValues([true, false])
            ->setDefault(true)
            ->getOption();

        return new FixerConfigurationResolver($options);
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
            $this->configuration[self::LINE_LENGHT_OPTION],
            $this->configuration[self::BREAK_LONG_LINES_OPTION],
            $this->configuration[self::INLINE_SHORT_LINES_OPTION]
        );
    }

    private function shouldSkip(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        // no items inside => skip
        if (($blockInfo->getEnd() - $blockInfo->getStart()) <= 1) {
            return true;
        }

        // nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($blockInfo->getStart());
        $nextToken = $tokens[$nextTokenPosition];

        if (Strings::startsWith($nextToken->getContent(), '<<<')) {
            return true;
        }

        // is array with indexed values "=>"
        if ($tokens->findGivenKind(T_DOUBLE_ARROW, $blockInfo->getStart(), $blockInfo->getEnd())) {
            return true;
        }

        // has comments => dangerous to change: https://github.com/Symplify/Symplify/issues/973
        if ($tokens->findGivenKind(T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd())) {
            return true;
        }

        return false;
    }

    /**
     * We go through tokens from down to up,
     * so we need to find ")" and then the start of function
     */
    private function matchNamePositionForEndOfFunctionCall(Tokens $tokens, Token $token, int $position): ?int
    {
        try {
            $blockStart = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $position);
        } catch (Throwable $throwable) {
            // not a block start
            return null;
        }

        $previousTokenPosition = $blockStart - 1;
        $possibleMethodNameToken = $tokens[$previousTokenPosition];

        // not a "methodCall()"
        if (! $possibleMethodNameToken->isGivenKind(T_STRING)) {
            return null;
        }

        // starts with small letter?
        $methodOrFunctionName = $possibleMethodNameToken->getContent();
        if (! ctype_lower($methodOrFunctionName[0])) {
            return null;
        }

        // is "someCall()"? we don't care, there are no arguments
        if ($tokens[$blockStart + 1]->equals(')')) {
            return null;
        }

        return $previousTokenPosition;
    }

    private function processMethodCall(Tokens $tokens, int $position): void
    {
        $token = $tokens[$position];

        $methodNamePosition = $this->matchNamePositionForEndOfFunctionCall($tokens, $token, $position);
        if ($methodNamePosition === null) {
            return;
        }

        $blockInfo = $this->blockFinder->findInTokensByPositionAndContent($tokens, $methodNamePosition, '(');
        if ($blockInfo === null) {
            return;
        }

        // has comments => dangerous to change: https://github.com/Symplify/Symplify/issues/973
        if ($tokens->findGivenKind(T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd())) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition(
            $blockInfo,
            $tokens,
            $this->configuration[self::LINE_LENGHT_OPTION],
            $this->configuration[self::BREAK_LONG_LINES_OPTION],
            $this->configuration[self::INLINE_SHORT_LINES_OPTION]
        );
    }
}
