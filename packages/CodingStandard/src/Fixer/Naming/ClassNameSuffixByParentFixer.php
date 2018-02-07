<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;

final class ClassNameSuffixByParentFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private const PARENT_CLASS_TO_SUFFIXES_MAP_OPTION = 'parent_classes_to_suffixes';

    /**
     * @var mixed[]
     */
    private $configuration = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Class should respect suffix by parent class', [
            new CodeSample(
                '<?php
class SomeClass extends Command
{
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_STRING, T_EXTENDS, T_CLASS]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classTokensAnalyzer = ClassWrapper::createFromTokensArrayStartPosition($tokens, $index);
            dump($classTokensAnalyzer->getName());
            dump($classTokensAnalyzer->getParentClassName());
            die;
        }
    }

    public function isRisky(): bool
    {
        return true;
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
            self::PARENT_CLASS_TO_SUFFIXES_MAP_OPTION,
            'Map of parent classes to suffixes, that their children should have'
        );

        return new FixerConfigurationResolver([$fixerOptionBuilder->setAllowedTypes(['array'])->getOption()]);
    }
}
