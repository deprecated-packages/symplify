<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Configuration\Configuration;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\MethodCallWrapperFactory;
use Throwable;

final class BreakMethodCallsFixer implements DefinedFixerInterface
{
    /**
     * @var MethodCallWrapperFactory
     */
    private $methodCallWrapperFactory;

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
        MethodCallWrapperFactory $methodCallWrapperFactory,
        LineLengthTransformer $lineLengthTransformer
    ) {
        $this->methodCallWrapperFactory = $methodCallWrapperFactory;
        $this->configuration = $configuration;
        $this->lineLengthTransformer = $lineLengthTransformer;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Method and function call parameters should be on the same/standalone line to fit line length.',
            [
                new CodeSample(
                    '<?php
    $someClass = new SomeClass;
    $someClass->someMethod($superLongArgument, $superLongArgument, $superLongArgument, $superLongArgument);'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_STRING, ',', '(', ')']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            $methodNamePosition = $this->matchNamePositionForEndOfFunctionCall($tokens, $token, $position);
            if ($methodNamePosition === null) {
                continue;
            }

            $this->fixMethodCall($methodNamePosition, $tokens);
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

    private function fixMethodCall(int $position, Tokens $tokens): void
    {
        $methodCallWrapper = $this->methodCallWrapperFactory->createFromTokensAndPosition($tokens, $position);

        if ($methodCallWrapper->getFirstLineLength() > $this->configuration->getMaxLineLength()) {
            $this->lineLengthTransformer->prepareIndentWhitespaces($tokens, $position);

            $start = $methodCallWrapper->getArgumentsBracketStart();
            $end = $methodCallWrapper->getArgumentsBracketEnd();

            $this->lineLengthTransformer->breakItems($start, $end, $tokens);
            return;
        }

        if ($methodCallWrapper->getLineLengthToEndOfArguments() <= $this->configuration->getMaxLineLength()) {
            $this->lineLengthTransformer->inlineItems($methodCallWrapper->getArgumentsBracketEnd(), $tokens, $position);
        }
    }

    /**
     * We go throught tokens from down to up,
     * so we need to find ")" and then the start of function
     */
    private function matchNamePositionForEndOfFunctionCall(Tokens $tokens, Token $token, int $position): ?int
    {
        if ($token->getContent() !== ')') {
            return null;
        }

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
}
