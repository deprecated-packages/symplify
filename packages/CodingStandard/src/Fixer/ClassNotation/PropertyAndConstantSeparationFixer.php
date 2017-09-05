<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ClassNotation;

use PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use ReflectionClass;
use SplFileInfo;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class PropertyAndConstantSeparationFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private const SPACE_COUNT_OPTION = 'space_count';

    /**
     * @var int
     */
    private const DEFAULT_SPACE_COUNT = 1;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var mixed[]
     */
    private $configuration = [];

    public function __construct()
    {
        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $summary = sprintf(
            'Properties and constants must be separated by %d blank line(s).',
            $this->configuration[self::SPACE_COUNT_OPTION]
        );

        return new FixerDefinition(
            $summary,
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

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        if ($configuration === null) {
            return;
        }

        $this->configuration = $this->getConfigurationDefinition()
            ->resolve($configuration);
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $fixerOptionBuilder = new FixerOptionBuilder(
            self::SPACE_COUNT_OPTION,
            'Number of spaces between constant and property elements.'
        );

        $spaceCountOption = $fixerOptionBuilder->setAllowedTypes(['int'])
            ->setDefault(self::DEFAULT_SPACE_COUNT)
            ->getOption();

        return new FixerConfigurationResolver([$spaceCountOption]);
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
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::fixSpaceBelowMethod().
     *
     * This is nasty solution to prevent BC breaks and include fixes from
     * @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer.
     *
     * Don't to this at home!
     */
    private function fixSpacesBelow(Tokens $tokens, int $classEnd, int $constantOrPropertyEnd): void
    {
        $nextNotWhitePosition = $tokens->getNextNonWhitespace($constantOrPropertyEnd);
        if ($nextNotWhitePosition === null) {
            return;
        }

        $methodSeparationFixer = new MethodSeparationFixer;
        $methodSeparationFixer->setWhitespacesConfig($this->whitespacesFixerConfig);
        $methodSeparationFixerClassReflection = new ReflectionClass(MethodSeparationFixer::class);

        $correctLineBreaksMethodReflection = $methodSeparationFixerClassReflection->getMethod('correctLineBreaks');
        $correctLineBreaksMethodReflection->setAccessible(true);

        $arguments = [
            $tokens,
            $constantOrPropertyEnd,
            $nextNotWhitePosition,
            $nextNotWhitePosition === $classEnd ? $this->configuration[self::SPACE_COUNT_OPTION] : $this->configuration[self::SPACE_COUNT_OPTION] + 1,
        ];

        $correctLineBreaksMethodReflection->invoke($methodSeparationFixer, ...$arguments);
    }
}
