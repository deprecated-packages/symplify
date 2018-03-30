<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndFinder;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;

final class LineLengthFixer implements DefinedFixerInterface
{
    /**
     * @var int[]|string[]
     */
    private const BLOCK_START_TOKENS = [
        T_ARRAY, // "["
        CT::T_ARRAY_SQUARE_BRACE_OPEN, // "array"(
        '('
    ];

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockStartAndEndFinder
     */
    private $blockStartAndEndFinder;

    public function __construct(
        LineLengthTransformer $lineLengthTransformer,
        BlockStartAndEndFinder $blockStartAndEndFinder
    ) {
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockStartAndEndFinder = $blockStartAndEndFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array items, method arguments and new arguments should be on same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
$array = ["loooooooooooooooooooooooooooooooongArraaaaaaaaaaay", "looooooooooooooooooooooooooooooooongArraaaaaaaaaaay"];'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::BLOCK_START_TOKENS);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if (! $token->isGivenKind(self::BLOCK_START_TOKENS) && $token->getContent() !== '(') {
                continue;
            }

            $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByBlockStart($tokens, $position);
            if ($blockStartAndEndInfo === null) {
                continue;
            }

            $this->lineLengthTransformer->fixStartPositionToEndPosition($blockStartAndEndInfo, $tokens, $position);
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }
}
