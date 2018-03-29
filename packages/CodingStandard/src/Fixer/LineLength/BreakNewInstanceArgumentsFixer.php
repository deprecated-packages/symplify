<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndFinder;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndInfo;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;

final class BreakNewInstanceArgumentsFixer implements DefinedFixerInterface
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
        return new FixerDefinition('New instance arguments should be on the same/standalone line to fit line length.', [
            new CodeSample(
                '<?php $someObject = new SomeClass($superLongArguments, $anotherLongArguments, $andLittleMore);'
            ),
            new CodeSample(
                '<?php $someObject = new SomeClass(
                    $short,
                    $args
                );'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_NEW, '(', T_STRING, ')']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if (! $token->isGivenKind(T_NEW)) {
                continue;
            }

            $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByPositionAndContent(
                $tokens,
                $position,
                '('
            );
            if ($blockStartAndEndInfo === null) {
                continue;
            }

            if ($this->shouldSkip($tokens, $blockStartAndEndInfo)) {
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

    /**
     * Execute before \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer (with priority 0)
     */
    public function getPriority(): int
    {
        return 5;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function shouldSkip(Tokens $tokens, BlockStartAndEndInfo $blockStartAndEndInfo): bool
    {
        // no arguments => skip
        if (($blockStartAndEndInfo->getEnd() - $blockStartAndEndInfo->getStart()) <= 1) {
            return true;
        }

        // nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($blockStartAndEndInfo->getStart());
        $nextToken = $tokens[$nextTokenPosition];
        return Strings::startsWith($nextToken->getContent(), '<<<');
    }
}
