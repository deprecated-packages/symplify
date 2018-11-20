<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;

final class StandaloneLineInMultilineArrayFixer extends AbstractSymplifyFixer
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var ArrayWrapperFactory
     */
    private $arrayWrapperFactory;

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(
        ArrayWrapperFactory $arrayWrapperFactory,
        LineLengthTransformer $lineLengthTransformer,
        BlockFinder $blockFinder
    ) {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockFinder = $blockFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Indexed PHP arrays with 2 and more items should have 1 item per line.', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isAllTokenKindsFound([T_DOUBLE_ARROW, ',']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);

            $arrayWrapper = $this->arrayWrapperFactory->createFromTokensAndBlockInfo($tokens, $blockInfo);

            if ($this->shouldSkip($arrayWrapper)) {
                continue;
            }

            $this->lineLengthTransformer->breakItems($blockInfo, $tokens);
        }
    }

    private function shouldSkip(ArrayWrapper $arrayWrapper): bool
    {
        if (! $arrayWrapper->isAssociativeArray()) {
            return true;
        }

        return $arrayWrapper->getItemCount() <= 1;
    }
}
