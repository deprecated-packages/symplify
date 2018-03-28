<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Configuration\Configuration;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;

final class BreakArrayListFixer implements DefinedFixerInterface
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
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    public function __construct(
        Configuration $configuration,
        ArrayWrapperFactory $arrayWrapperFactory,
        LineLengthTransformer $lineLengthTransformer
    ) {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->configuration = $configuration;
        $this->lineLengthTransformer = $lineLengthTransformer;
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

            $arrayWrapper = $this->arrayWrapperFactory->createFromTokensArrayStartPosition($tokens, $position);
            if ($arrayWrapper->isAssociativeArray()) {
                continue;
            }

            $this->fixArray($position, $tokens, $arrayWrapper);
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

    private function fixArray(int $position, Tokens $tokens, ArrayWrapper $arrayWrapper): void
    {
        if ($arrayWrapper->getFirstLineLength() > $this->configuration->getMaxLineLength()) {
            $this->lineLengthTransformer->prepareIndentWhitespaces($tokens, $position);
            $this->lineLengthTransformer->breakItems(
                $arrayWrapper->getStartIndex(),
                $arrayWrapper->getEndIndex(),
                $tokens
            );
        }
    }
}
