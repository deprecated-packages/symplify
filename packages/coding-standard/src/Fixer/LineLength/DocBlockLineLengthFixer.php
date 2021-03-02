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

            [$descriptionLines, $otherLines] = $this->splitLines($docBlockLines);
            if (count($descriptionLines) === 0) {
                continue;
            }

            $description = trim(implode(' ', $descriptionLines));
            $wrappedDescription = wordwrap($description, $maximumLineLength);
            if (count($otherLines) > 0) {
                $wrappedDescription .= "\n";
            }

            $reformattedLines = array_merge(explode(PHP_EOL, $wrappedDescription), $otherLines);

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
        if (preg_match('/^([\s]*) \*/m', $docBlock, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param string $docBlock
     * @return string[]
     */
    private function getDocBlockLines(string $docBlock): array
    {
        // Remove the prefix '/**'
        $docBlock = Strings::replace($docBlock, '/^(\/\*\*[\n]?)/');
        // Remove the suffix '*/'
        $docBlock = Strings::replace($docBlock, '/(\*\/)$/');
        // Remove extra whitespace at the end
        $docBlock = rtrim($docBlock);

        $docBlockLines = explode(PHP_EOL, $docBlock);

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
        array_push($docBlockLines, $indentationString . ' */');

        return implode(PHP_EOL, $docBlockLines);
    }

    /**
     * @param string[] $docBlockLines
     * @return array<{string[]},{string[]}>
     */
    private function splitLines(array $docBlockLines): array
    {
        $descriptionLines = [];
        $otherLines = [];

        $collectDescriptionLines = true;

        foreach ($docBlockLines as $docBlockLine) {
            if (Strings::startsWith($docBlockLine, '@')) {
                $collectDescriptionLines = false;
            }

            if ($collectDescriptionLines) {
                $descriptionLines[] = $docBlockLine;
            } else {
                $otherLines[] = $docBlockLine;
            }
        }

        return [$descriptionLines, $otherLines];
    }
}
