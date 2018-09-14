<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Php;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use function Safe\sprintf;
use function Safe\substr;

final class ClassStringToClassConstantFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    public const CLASS_MUST_EXIST_OPTION = 'class_must_exist';

    /**
     * @var string
     */
    public const ALLOW_CLASES_OPTION = 'allow_classes';

    /**
     * @var string
     */
    private const CLASS_PART_PATTERN = '[A-Z]\w*[a-z]\w*';

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
        return new FixerDefinition(
            '`::class` references should be used over string for classes and interfaces.',
            [
                new CodeSample('<?php $className = "DateTime";'),
                new CodeSample('<?php $interfaceName = "DateTimeInterface";'),
                new CodeSample('<?php $interfaceName = "Nette\Utils\DateTime";'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $revertedTokens */
        $revertedTokens = array_reverse($tokens->toArray(), true);

        foreach ($revertedTokens as $index => $token) {
            if (! $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $potentialClassInterfaceOrTrait = $this->getNameFromToken($token);
            $potentialClassInterfaceOrTrait = str_replace('\\\\', '\\', $potentialClassInterfaceOrTrait);

            if (! $this->isClassInterfaceOrTrait($potentialClassInterfaceOrTrait)) {
                continue;
            }

            unset($tokens[$index]);
            $tokens->insertAt($index, $this->convertNameToTokens($potentialClassInterfaceOrTrait));
        }
    }

    /**
     * Run before @see \Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer
     */
    public function getPriority(): int
    {
        return 15;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function isRisky(): bool
    {
        return false;
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
            self::CLASS_MUST_EXIST_OPTION,
            'Whether class has to exist or not.'
        );

        $classMustExistOption = $fixerOptionBuilder->setAllowedValues([true, false])
            ->setDefault(true)
            ->getOption();

        $fixerOptionBuilder = new FixerOptionBuilder(
            self::ALLOW_CLASES_OPTION,
            'Classes allowed to be in string format.'
        );

        $allowedClassesOption = $fixerOptionBuilder->setAllowedTypes(['array'])
            ->setDefault([])
            ->getOption();

        return new FixerConfigurationResolver([$classMustExistOption, $allowedClassesOption]);
    }

    private function getNameFromToken(Token $token): string
    {
        // remove quotes "" around the string
        $name = substr($token->getContent(), 1, -1);

        // remove "\" prefix
        return ltrim($name, '\\');
    }

    private function isClassInterfaceOrTrait(string $potentialClassInterfaceOrTrait): bool
    {
        if ($potentialClassInterfaceOrTrait === '') {
            return false;
        }

        // lowercase string are not classes; required because class_exists() is case-insensitive
        if (ctype_lower($potentialClassInterfaceOrTrait[0])) {
            return false;
        }

        foreach ($this->configuration[self::ALLOW_CLASES_OPTION] as $allowedClass) {
            if ($potentialClassInterfaceOrTrait === $allowedClass) {
                return false;
            }
        }

        $accepted = class_exists($potentialClassInterfaceOrTrait)
            || interface_exists($potentialClassInterfaceOrTrait)
            || trait_exists($potentialClassInterfaceOrTrait);

        if ($this->configuration[self::CLASS_MUST_EXIST_OPTION] === false) {
            $accepted = (bool) Strings::match($potentialClassInterfaceOrTrait, $this->getClassyPattern());
        }

        return $accepted;
    }

    private function convertNameToTokens(string $classInterfaceOrTraitName): Tokens
    {
        $tokens = [];

        $parts = explode('\\', $classInterfaceOrTraitName);
        foreach ($parts as $part) {
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            $tokens[] = new Token([T_STRING, $part]);
        }

        $tokens[] = new Token([T_DOUBLE_COLON, '::']);
        $tokens[] = new Token([CT::T_CLASS_CONSTANT, 'class']);

        return Tokens::fromArray($tokens);
    }

    private function getClassyPattern(): string
    {
        return sprintf('#^%s(\\\\%s)+\z#', self::CLASS_PART_PATTERN, self::CLASS_PART_PATTERN);
    }
}
