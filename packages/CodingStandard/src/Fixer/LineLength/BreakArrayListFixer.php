<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndFinder;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;

final class BreakArrayListFixer implements DefinedFixerInterface
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

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
        return new FixerDefinition('Array items should be on same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
$array = ["loooooooooooooooooooooooooooooooongArraaaaaaaaaaay", "looooooooooooooooooooooooooooooooongArraaaaaaaaaaay"];'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isTokenKindFound('=');
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $position => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            [$blockStart, $blockEnd] = $this->blockStartAndEndFinder->findInTokensByBlockStart($tokens, $position);

            $this->lineLengthTransformer->fixStartPositionToEndPosition($blockStart, $blockEnd, $tokens, $position);
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
