<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ClassNotation;

use PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use ReflectionClass;
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
            if ($constantOrPropertyEnd) {
                $this->fixSpacesBelow($tokens, $classTokensAnalyzer->getClassEnd(), $constantOrPropertyEnd);
            }
        }
    }

    /**
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::fixSpacesBelow().
     */
    private function fixSpacesBelow(Tokens $tokens, int $classEnd, int $constantOrPropertyEnd): void
    {
        $nextNotWhite = $tokens->getNextNonWhitespace($constantOrPropertyEnd);
        if ($nextNotWhite === null) {
            return;
        }

        $this->callClassPrivateMethod(
            MethodSeparationFixer::class,
            'correctLineBreaks',
            $tokens,
            $constantOrPropertyEnd,
            $nextNotWhite,
            $nextNotWhite === $classEnd ? 1 : 2
        );
    }

    /**
     * @param mixed[] ...$arguments
     */
    private function callClassPrivateMethod(string $class, string $method, ...$arguments): void
    {
        $classReflection = new ReflectionClass($class);
        $methodReflection = $classReflection->getMethod($method);
        $methodReflection->setAccessible(true);
        $methodReflection->invoke(new $class, ...$arguments);
    }
}
