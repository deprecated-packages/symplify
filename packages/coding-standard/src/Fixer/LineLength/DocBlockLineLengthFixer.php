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

            $docBlockLines = explode(PHP_EOL, $token->getContent());
            foreach ($docBlockLines as $docBlockLine) {
                if (Strings::length($docBlockLine) <= $this->lineLength) {
                    continue;
                }

                $extraDocBlockLines = str_split($docBlockLine, $this->lineLength);
                $extraDocBlockLines = $this->standardizeExtraLines($extraDocBlockLines);
                array_splice($docBlockLines, 1, 1, $extraDocBlockLines);
            }

            $newDocBlockContent = implode(PHP_EOL, $docBlockLines);
            if ($token->getContent() === $newDocBlockContent) {
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

    /**
     * @param string[] $extraDocBlockLines
     * @return string[]
     */
    private function standardizeExtraLines(array $extraDocBlockLines): array
    {
        $extraDocBlockLines = $this->prependLinesWithAsterisk($extraDocBlockLines);
        return $this->rtrimLines($extraDocBlockLines);
    }

    /**
     * @param string[] $extraDocBlockLines
     * @return string[]
     */
    private function prependLinesWithAsterisk(array $extraDocBlockLines): array
    {
        foreach ($extraDocBlockLines as $extraKey => $extraDocBlockLine) {
            if ($extraKey === 0) {
                continue;
            }

            $extraDocBlockLines[$extraKey] = ' * ' . $extraDocBlockLine;
        }

        return $extraDocBlockLines;
    }

    /**
     * @param string[] $lines
     * @return string[]
     */
    private function rtrimLines(array $lines): array
    {
        foreach ($lines as $extraKey => $extraDocBlockLine) {
            $lines[$extraKey] = rtrim($extraDocBlockLine);
        }

        return $lines;
    }
}
