<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Solid;

use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

final class FinalInterfaceFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private const ONLY_INTERFACES_OPTION = 'only_interfaces';

    /**
     * @var mixed[]
     */
    private $configuration = [];

    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    public function __construct(ClassWrapperFactory $classWrapperFactory)
    {
        $this->classWrapperFactory = $classWrapperFactory;

        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Non-abstract class that implements interface should be final.',
            [new CodeSample('
<?php
class SomeClass implements SomeInterface {};')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_STRING, T_IMPLEMENTS, '{', '}'])
            && ! $tokens->isTokenKindFound(T_FINAL);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $tokensReversed = array_reverse(iterator_to_array($tokens), true);

        /** @var Token $token */
        foreach ($tokensReversed as $index => $token) {
            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
            if ($this->shouldBeSkipped($classWrapper)) {
                continue;
            }

            $this->fixClass($tokens, $index);
        }
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Classes implementing interface that are further extended
     * can break the code.
     */
    public function isRisky(): bool
    {
        return true;
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
        $option = (new FixerOptionBuilder(self::ONLY_INTERFACES_OPTION, 'List of interfaces to check.'))
            ->setDefault([])
            ->getOption();

        return new FixerConfigurationResolver([$option]);
    }

    private function fixClass(Tokens $tokens, int $position): void
    {
        $tokens->insertAt($position, [new Token([T_FINAL, 'final']), new Token([T_WHITESPACE, ' '])]);
    }

    private function shouldBeSkipped(ClassWrapper $classWrapper): bool
    {
        if (! $classWrapper->implementsInterface()) {
            return true;
        }

        if ($classWrapper->isFinal() || $classWrapper->isAbstract()) {
            return true;
        }

        if ($classWrapper->isDoctrineEntity()) {
            return true;
        }

        if ($this->configuration[self::ONLY_INTERFACES_OPTION]) {
            $interfaces = $this->configuration[self::ONLY_INTERFACES_OPTION];

            return ! (bool) array_intersect($interfaces, $classWrapper->getInterfaceNames());
        }

        return false;
    }
}
