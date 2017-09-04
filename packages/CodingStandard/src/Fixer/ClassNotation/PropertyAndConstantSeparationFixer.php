<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ClassNotation;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

/**
 * @todo change for methods as well?
 * @todo make configurable? usable in Nette etc.?
 */
final class PropertyAndConstantSeparationFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Properties and constants must be separated with one blank line.',
            [
                new CodeSample(
                    '<?php
class SomeClass
{
    public $firstProperty;
    public $secondProperty;
}'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            if (! $tokens[$index]->isClassy()) {
                continue;
            }

            $classTokensAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);
            $this->fixClass($tokens, $classTokensAnalyzer);
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
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::getPriority().
     *
     * Must run before BracesFixer and IndentationTypeFixer fixers because this fixer
     * might add line breaks to the code without indenting.
     */
    public function getPriority(): int
    {
        return 55;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    private function fixClass(Tokens $tokens, ClassTokensAnalyzer $classTokensAnalyzer): void
    {
        $propertiesAndConstants = $classTokensAnalyzer->getPropertiesAndConstants();
        foreach ($propertiesAndConstants as $index => $propertyOrConstantToken) {
            $constantOrPropertyEnd = $tokens->getNextTokenOfKind($index, [';']);
            $this->fixSpacesBelow($tokens, $classTokensAnalyzer->getClassEnd(), $constantOrPropertyEnd);
        }
    }

    /**
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::fixSpacesBelow().
     * @todo maybe rather reflection?
     */
    private function fixSpacesBelow(Tokens $tokens, int $classEnd, int $constantOrPropertyEnd): void
    {
        $nextNotWhite = $tokens->getNextNonWhitespace($constantOrPropertyEnd);
        $this->correctLineBreaks($tokens, $constantOrPropertyEnd, $nextNotWhite, $nextNotWhite === $classEnd ? 1 : 2);
    }

    /**
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::correctLineBreaks().
     */
    private function correctLineBreaks(Tokens $tokens, int $startIndex, int $endIndex, int $reqLineCount = 2): void
    {
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

        ++$startIndex;
        $numbOfWhiteTokens = $endIndex - $startIndex;
        if ($numbOfWhiteTokens === 0) {
            $tokens->insertAt($startIndex, new Token([T_WHITESPACE, str_repeat($lineEnding, $reqLineCount)]));

            return;
        }

        $lineBreakCount = $this->getLineBreakCount($tokens, $startIndex, $endIndex);
        if ($reqLineCount === $lineBreakCount) {
            return;
        }

        if ($lineBreakCount < $reqLineCount) {
            $tokens[$startIndex] = new Token([
                T_WHITESPACE,
                str_repeat($lineEnding, $reqLineCount - $lineBreakCount) . $tokens[$startIndex]->getContent(),
            ]);

            return;
        }

        // $lineCount = > $reqLineCount : check the one Token case first since this one will be true most of the time
        if ($numbOfWhiteTokens === 1) {
            $tokens[$startIndex] = new Token([
                T_WHITESPACE,
                preg_replace('/\r\n|\n/', '', $tokens[$startIndex]->getContent(), $lineBreakCount - $reqLineCount),
            ]);

            return;
        }

        // $numbOfWhiteTokens = > 1
        $toReplaceCount = $lineBreakCount - $reqLineCount;
        for ($i = $startIndex; $i < $endIndex && $toReplaceCount > 0; ++$i) {
            $tokenLineCount = substr_count($tokens[$i]->getContent(), "\n");
            if ($tokenLineCount > 0) {
                $tokens[$i] = new Token([
                    T_WHITESPACE,
                    preg_replace('/\r\n|\n/', '', $tokens[$i]->getContent(), min($toReplaceCount, $tokenLineCount)),
                ]);
                $toReplaceCount -= $tokenLineCount;
            }
        }
    }

    /**
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::getLineBreakCount().
     */
    private function getLineBreakCount(Tokens $tokens, int $whiteStart, int $whiteEnd): int
    {
        $lineCount = 0;
        for ($i = $whiteStart; $i < $whiteEnd; ++$i) {
            $lineCount += substr_count($tokens[$i]->getContent(), "\n");
        }

        return $lineCount;
    }
}
