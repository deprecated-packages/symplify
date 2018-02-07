<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ClassNotation;

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
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
use SplFileInfo;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;

/**
 * @deprecated Will be removed in Symplify 4.0
 */
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
        trigger_error(
            sprintf(
                '"%s" is deprecated and will be removed in Symplify 4.0. ' .
                    'Use "%s" that does the similar job instead or extend it."',
                self::class,
                ClassAttributesSeparationFixer::class
            ),
            E_USER_DEPRECATED
        );
        sleep(3); // inspired at "deprecated interface" Tweet

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
            && $tokens->isAllTokenKindsFound([T_VARIABLE, T_FUNCTION])
            && $tokens->isAnyTokenKindsFound([T_PUBLIC, T_PROTECTED, T_PRIVATE]);
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
     * Run after @see \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer
     * to give these spaces priority.
     */
    public function getPriority(): int
    {
        return 50;
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
     * Same as @see \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::correctLineBreaks().
     *
     * This solution prevents BC breaks and include code updates.
     */
    private function fixSpacesBelow(Tokens $tokens, int $classEnd, int $propertyEnd): void
    {
        $nextNotWhitePosition = $tokens->getNextNonWhitespace($propertyEnd);
        if ($nextNotWhitePosition === null) {
            return;
        }

        $classAttributesSeparationFixer = new ClassAttributesSeparationFixer();
        $classAttributesSeparationFixer->setWhitespacesConfig($this->whitespacesFixerConfig);

        $arguments = [
            $tokens,
            $propertyEnd,
            $nextNotWhitePosition,
            $nextNotWhitePosition === $classEnd ? $this->configuration[self::SPACE_COUNT_OPTION] : $this->configuration[self::SPACE_COUNT_OPTION] + 1,
        ];

        (new PrivatesCaller())->callPrivateMethod(
            $classAttributesSeparationFixer,
            'correctLineBreaks',
            ...$arguments
        );
    }
}
