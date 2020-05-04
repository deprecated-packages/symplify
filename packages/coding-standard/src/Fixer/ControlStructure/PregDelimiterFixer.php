<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ControlStructure;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

/**
 * @deprecated
 */
final class PregDelimiterFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/pd1KvP/1/
     */
    private const INNER_PATTERN_PATTERN = '#(?<open>[\'|"])(?<content>.*?)(?<close>[A-z]*[\'|"])#';

    /**
     * All with pattern as 1st argument
     * @var int[]
     */
    private const FUNCTIONS_WITH_REGEX_PATTERN = [
        'preg_match' => 0,
        'preg_replace_callback_array' => 0,
        'preg_replace_callback' => 0,
        'preg_replace' => 0,
        'preg_match_all' => 0,
        'preg_split' => 0,
        'preg_grep' => 0,
    ];

    /**
     * All with pattern as 2st argument
     * @var int[][]
     */
    private const STATIC_METHODS_WITH_REGEX_PATTERN = [
        'Strings' => [
            'match' => 1,
            'matchAll' => 1,
            'replace' => 1,
            'split' => 1,
        ],
    ];

    /**
     * @var string
     */
    private $delimiter = '#';

    /**
     * @var ArgumentsAnalyzer
     */
    private $argumentsAnalyzer;

    public function __construct(ArgumentsAnalyzer $argumentsAnalyzer)
    {
        $this->argumentsAnalyzer = $argumentsAnalyzer;

        trigger_error(sprintf(
            'Fixer "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" instead',
            self::class,
            'https://github.com/rectorphp/rector/blob/master/docs/AllRectorsOverview.md#consistentpregdelimiterrector'
        ));

        sleep(3);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'preg_match() and similar delimiters should be consistent.',
            [new CodeSample('preg_match("~value~", $content);')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_STRING]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $position => $token) {
            if (! $token->isGivenKind(T_STRING)) {
                continue;
            }

            $regexArgumentPosition = $this->resolveRegexArgumentPosition($tokens, $token, $position);

            if ($regexArgumentPosition === null) {
                continue;
            }

            $argumentInfos = $this->resolveArgumentsInfoForFunction($tokens, $position);
            if (! isset($argumentInfos[$regexArgumentPosition])) {
                continue;
            }

            $argumentInfo = $argumentInfos[$regexArgumentPosition];
            $this->processArgumentInfo($argumentInfo, $tokens);
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        if (isset($configuration['delimiter']) && $configuration['delimiter']) {
            $this->delimiter = $configuration['delimiter'];
        }
    }

    private function resolveRegexArgumentPosition(Tokens $tokens, Token $token, int $position): ?int
    {
        if (isset(self::FUNCTIONS_WITH_REGEX_PATTERN[$token->getContent()])) {
            return self::FUNCTIONS_WITH_REGEX_PATTERN[$token->getContent()];
        }

        foreach (self::STATIC_METHODS_WITH_REGEX_PATTERN as $class => $methodNameToArgumentPosition) {
            foreach ($methodNameToArgumentPosition as $methodName => $argumentPosition) {
                // not a static call
                if (! $this->isStaticCall($tokens, $position, $class, $methodName)) {
                    continue;
                }

                return $argumentPosition;
            }
        }

        return null;
    }

    /**
     * @return ArgumentAnalysis[]
     */
    private function resolveArgumentsInfoForFunction(Tokens $tokens, int $position): array
    {
        /** @var int $openingBracketPosition */
        $openingBracketPosition = $tokens->getNextTokenOfKind($position, ['(']);
        /** @var int $closingBracketPosition */
        $closingBracketPosition = $tokens->getNextTokenOfKind($position, [')']);

        $argumentPositions = $this->argumentsAnalyzer->getArguments(
            $tokens,
            $openingBracketPosition,
            $closingBracketPosition
        );

        $argumentInfos = [];
        foreach ($argumentPositions as $start => $end) {
            $argumentInfos[] = $this->argumentsAnalyzer->getArgumentInfo($tokens, $start, $end);
        }

        return $argumentInfos;
    }

    private function processArgumentInfo(ArgumentAnalysis $argumentAnalysis, Tokens $tokens): void
    {
        if ($argumentAnalysis->getTypeAnalysis() === null) {
            return;
        }

        $argumentPosition = $argumentAnalysis->getTypeAnalysis()->getStartIndex();
        if (! $tokens[$argumentPosition]->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            return;
        }

        $argumentValue = $argumentAnalysis->getTypeAnalysis()->getName();
        $newArgumentValue = Strings::replace(
            $argumentValue,
            self::INNER_PATTERN_PATTERN,
            function (array $match): string {
                $innerPattern = $match['content'];

                if (strlen($innerPattern) > 2 && $innerPattern[0] === $innerPattern[strlen($innerPattern) - 1]) {
                    $innerPattern[0] = $this->delimiter;
                    $innerPattern[strlen($innerPattern) - 1] = $this->delimiter;
                }

                return $match['open'] . $innerPattern . $match['close'];
            }
        );

        // no change
        if ($newArgumentValue === $argumentValue) {
            return;
        }

        $tokens[$argumentPosition] = new Token([T_CONSTANT_ENCAPSED_STRING, $newArgumentValue]);
    }

    private function isStaticCall(Tokens $tokens, int $position, string $class, string $method): bool
    {
        $token = $tokens[$position];
        if (! $tokens[$position - 1]->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
            return false;
        }

        if ($tokens[$position - 2]->getContent() !== $class) {
            return false;
        }

        return $token->getContent() === $method;
    }
}
