<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\ValueObject\DocBlockLines;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\DocBlockLineLengthFixer\DocBlockLineLengthFixerTest
 */
final class DocBlockLineLengthFixer extends AbstractSymplifyFixer implements ConfigurableRuleInterface, ConfigurableFixerInterface, DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    public const LINE_LENGTH = 'line_length';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Docblock lenght should fit expected width';

    /**
     * @see https://regex101.com/r/DNWfB6/1
     * @var string
     */
    private const INDENTATION_BEFORE_ASTERISK_REGEX = '/^(?<indentation>\s*) \*/m';

    /**
     * @see https://regex101.com/r/CUxOj5/1
     * @var string
     */
    private const BEGINNING_OF_DOC_BLOCK_REGEX = '/^(\/\*\*[\n]?)/';

    /**
     * @see https://regex101.com/r/otQGPe/1
     * @var string
     */
    private const END_OF_DOC_BLOCK_REGEX = '/(\*\/)$/';

    /**
     * @var int
     */
    private $lineLength = 120;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if (! $token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $docBlock = $token->getContent();
            $indentationString = $this->resolveIndentationStringFor($docBlock);

            $docBlockLines = $this->getDocBlockLines($docBlock);
            // The available line length is the configured line length, minus the existing indentation, minus ' * '
            $maximumLineLength = $this->lineLength - strlen($indentationString) - 3;

            $lines = $this->splitLines($docBlockLines);
            $descriptionLines = $lines->getDescriptionLines();
            if ($descriptionLines === []) {
                continue;
            }

            $paragraphs = $this->extractParagraphsFromDescriptionLines($descriptionLines);

            $lineWrappedParagraphs = array_map(
                function (string $paragraph) use ($maximumLineLength): string {
                    return wordwrap($paragraph, $maximumLineLength);
                },
                $paragraphs
            );

            $wrappedDescription = implode(PHP_EOL . PHP_EOL, $lineWrappedParagraphs);
            $otherLines = $lines->getOtherLines();
            if ($otherLines !== []) {
                $wrappedDescription .= "\n";
            }

            $reformattedLines = array_merge($this->getLines($wrappedDescription), $otherLines);

            $newDocBlockContent = $this->formatLinesAsDocBlockContent($reformattedLines, $indentationString);
            if ($docBlock === $newDocBlockContent) {
                continue;
            }

            $tokens[$position] = new Token([T_DOC_COMMENT, $newDocBlockContent]);
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->lineLength = $configuration[self::LINE_LENGTH] ?? 120;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
/**
 * Super long doc block description
 */
function some()
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * Super long doc
 * block description
 */
function some()
{
}
CODE_SAMPLE
                ,
                [
                    self::LINE_LENGTH => 40,
                ]
            ),
        ]);
    }

    private function resolveIndentationStringFor(string $docBlock): string
    {
        $matches = Strings::match($docBlock, self::INDENTATION_BEFORE_ASTERISK_REGEX);
        if ($matches === null) {
            return '';
        }

        return $matches['indentation'];
    }

    /**
     * @return string[]
     */
    private function getDocBlockLines(string $docBlock): array
    {
        // Remove the prefix '/**'
        $docBlock = Strings::replace($docBlock, self::BEGINNING_OF_DOC_BLOCK_REGEX);
        // Remove the suffix '*/'
        $docBlock = Strings::replace($docBlock, self::END_OF_DOC_BLOCK_REGEX);
        // Remove extra whitespace at the end
        $docBlock = rtrim($docBlock);

        $docBlockLines = $this->getLines($docBlock);

        return array_map(
            function (string $line): string {
                $noWhitespace = Strings::trim($line, Strings::TRIM_CHARACTERS);
                // Remove asterisks on the left side, plus additional whitespace
                return ltrim($noWhitespace, Strings::TRIM_CHARACTERS . '*');
            },
            $docBlockLines
        );
    }

    private function formatLinesAsDocBlockContent(array $docBlockLines, string $indentationString): string
    {
        foreach ($docBlockLines as $index => $docBlockLine) {
            $docBlockLines[$index] = $indentationString . ' *' . ($docBlockLine !== '' ? ' ' : '') . $docBlockLine;
        }

        array_unshift($docBlockLines, '/**');
        $docBlockLines[] = $indentationString . ' */';

        return implode(PHP_EOL, $docBlockLines);
    }

    /**
     * @param string[] $docBlockLines
     */
    private function splitLines(array $docBlockLines): DocBlockLines
    {
        $descriptionLines = [];
        $otherLines = [];

        $collectDescriptionLines = true;

        foreach ($docBlockLines as $docBlockLine) {
            if (Strings::startsWith($docBlockLine, '@')
                || Strings::startsWith($docBlockLine, '{@')) {
                // The line has a special meaning (it's an annotation, or something like {@inheritdoc})
                $collectDescriptionLines = false;
            }

            if ($collectDescriptionLines) {
                $descriptionLines[] = $docBlockLine;
            } else {
                $otherLines[] = $docBlockLine;
            }
        }

        return new DocBlockLines($descriptionLines, $otherLines);
    }

    /**
     * @return array<string>
     */
    private function extractParagraphsFromDescriptionLines(array $descriptionLines): array
    {
        $paragraphLines = [];
        $paragraphIndex = 0;

        foreach ($descriptionLines as $line) {
            if (! isset($paragraphLines[$paragraphIndex])) {
                $paragraphLines[$paragraphIndex] = [];
            }

            $line = trim($line);
            if ($line === '') {
                ++$paragraphIndex;
            } else {
                $paragraphLines[$paragraphIndex][] = $line;
            }
        }

        return array_map(function (array $lines): string {
            return implode(' ', $lines);
        }, $paragraphLines);
    }

    /**
     * @return string[]
     */
    private function getLines(string $string): array
    {
        return explode(PHP_EOL, $string);
    }
}
