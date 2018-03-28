<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndFinder;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;

final class BreakMethodArgumentsFixer implements DefinedFixerInterface
{
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
        return new FixerDefinition('Arguments should be on the same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
class SomeClass
{
    public function someMethod(SuperLongArguments $superLongArguments, AnotherLongArguments $anotherLongArguments)
    {
    }

    public function anotherMethod(
        ShortArgument $shortArgument,
        AnotherShortArgument $anotherShortArgument
    ) {
    }
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, ',', '(', ')'])
            && $tokens->isAnyTokenKindsFound([T_STRING, T_VARIABLE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if (! $token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            [$blockStart, $blockEnd] = $this->blockStartAndEndFinder->findInTokensByPositionAndContent(
                $tokens,
                $position,
                '('
            );

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
