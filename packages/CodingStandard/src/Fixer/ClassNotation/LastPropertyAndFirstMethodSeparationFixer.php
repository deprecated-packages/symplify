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
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use ReflectionClass;
use SplFileInfo;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;

final class LastPropertyAndFirstMethodSeparationFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface, ConfigurationDefinitionFixerInterface
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
            'Last property and first method must be separated by %d blank line(s).',
            $this->configuration[self::SPACE_COUNT_OPTION]
        );

        return new FixerDefinition(
            $summary,
            [
                new CodeSample(
                    '<?php
class SomeClass
{
    public $lastProperty;
    public function firstMethod()
    {
    }
}'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_TRAIT])
            && $tokens->isAllTokenKindsFound([T_VARIABLE, T_FUNCTION]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            if (! $tokens[$index]->isClassy()) {
                continue;
            }

            $classWrapper = ClassWrapper::createFromTokensArrayStartPosition($tokens, $index);
            $this->fixClass($tokens, $classWrapper);
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
            'Number of spaces between last property and first method.'
        );

        $spaceCountOption = $fixerOptionBuilder->setAllowedTypes(['int'])
            ->setDefault(self::DEFAULT_SPACE_COUNT)
            ->getOption();

        return new FixerConfigurationResolver([$spaceCountOption]);
    }

    private function fixClass(Tokens $tokens, ClassWrapper $classWrapper): void
    {
        $lastPropertyPosition = $classWrapper->getLastPropertyPosition();
        if ($lastPropertyPosition === null) {
            return;
        }

        $firstMethodPosition = $classWrapper->getFirstMethodPosition();
        if ($firstMethodPosition === null) {
            return;
        }

        $propertyEnd = $tokens->getNextTokenOfKind($lastPropertyPosition, [';']);
        if ($propertyEnd) {
            $this->fixSpacesBelow($tokens, $classWrapper->getClassEnd(), $propertyEnd);
        }
    }

    /**
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer::fixSpaceBelowMethod().
     *
     * This is nasty solution to prevent BC breaks and include code updates.
     *
     * Don't to this at home!
     */
    private function fixSpacesBelow(Tokens $tokens, int $classEnd, int $propertyEnd): void
    {
        $nextNotWhitePosition = $tokens->getNextNonWhitespace($propertyEnd);
        if ($nextNotWhitePosition === null) {
            return;
        }

        $methodSeparationFixer = new MethodSeparationFixer();
        $methodSeparationFixer->setWhitespacesConfig($this->whitespacesFixerConfig);
        $methodSeparationFixerClassReflection = new ReflectionClass(MethodSeparationFixer::class);

        $correctLineBreaksMethodReflection = $methodSeparationFixerClassReflection->getMethod('correctLineBreaks');
        $correctLineBreaksMethodReflection->setAccessible(true);

        $arguments = [
            $tokens,
            $propertyEnd,
            $nextNotWhitePosition,
            $nextNotWhitePosition === $classEnd ? $this->configuration[self::SPACE_COUNT_OPTION] : $this->configuration[self::SPACE_COUNT_OPTION] + 1,
        ];

        $correctLineBreaksMethodReflection->invoke($methodSeparationFixer, ...$arguments);
    }
}
