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
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;

final class BreakNewInstanceArgumentsFixer implements DefinedFixerInterface
{
    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    public function __construct(LineLengthTransformer $lineLengthTransformer)
    {
        $this->lineLengthTransformer = $lineLengthTransformer;
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

            $startBracketPosition = $tokens->getNextTokenOfKind($position, ['(']);
            if ($startBracketPosition === null) {
                continue;
            }

            // @todo: decouple som smart BlockStartEndFinder, where there is no need to seek
            // the type Tokens::BLOCK_TYPE_PARENTHESIS_BRACE manually, I never get it from the name
            $endBracketPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startBracketPosition);

            if ($this->shouldSkip($tokens, $startBracketPosition, $endBracketPosition)) {
                continue;
            }

            $this->lineLengthTransformer->fixStartPositionToEndPosition(
                $startBracketPosition,
                $endBracketPosition,
                $tokens,
                $position
            );
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

    private function shouldSkip(Tokens $tokens, int $startBracketPosition, int $endBracketPosition): bool
    {
        // no arguments => skip
        if (($endBracketPosition - $startBracketPosition) <= 1) {
            return true;
        }

        // nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($startBracketPosition);
        $nextToken = $tokens[$nextTokenPosition];

        return Strings::startsWith($nextToken->getContent(), '<<<');
    }
}
