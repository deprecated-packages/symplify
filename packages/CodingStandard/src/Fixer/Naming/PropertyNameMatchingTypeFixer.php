<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use Nette\Utils\Strings;
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
use Symplify\CodingStandard\FixerTokenWrapper\ArgumentWrapper;
use Symplify\CodingStandard\FixerTokenWrapper\MethodWrapper;
use Symplify\CodingStandard\FixerTokenWrapper\PropertyWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class PropertyNameMatchingTypeFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private const EXTRA_SKIPPED_CLASSES_OPTION = 'extra_skipped_classes';

    /**
     * @var string[]
     */
    public $skippedClasses = [
        '*DateTime*',
        'Spl*',
        'std*',
        'Iterator*',
        'SimpleXML*',
        '*|*', // union types
        '*[]', // arrays
    ];

    /**
     * @var ClassTokensAnalyzer|null
     */
    private $classTokenAnalyzer;

    /**
     * @var mixed[]
     */
    private $configuration = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Property name should match its type, if possible.', [
            new CodeSample(
                '<?php
class SomeClass
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING)
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (! $token->isClassy()) {
                $this->classTokenAnalyzer = null;

                continue;
            }

            $this->classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);

            $this->fixClassProperties($tokens);
            $this->fixClassMethods($tokens);
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
            self::EXTRA_SKIPPED_CLASSES_OPTION,
            'Classes that are skipped using fnmatch().'
        );

        $skippedClassesOption = $fixerOptionBuilder->setAllowedTypes(['string'])
            ->getOption();

        return new FixerConfigurationResolver([$skippedClassesOption]);
    }

    private function fixClassProperties(Tokens $tokens): void
    {
        $changedPropertyNames = [];

        foreach ($this->classTokenAnalyzer->getProperties() as $propertyIndex => $propertyToken) {
            $propertyWrapper = PropertyWrapper::createFromTokensAndPosition($tokens, $propertyIndex);

            $oldName = $propertyWrapper->getName();
            if ($propertyWrapper->getType() === null || ! $propertyWrapper->isClassType()) {
                continue;
            }

            if ($this->isAllowedNameOrType($oldName, $propertyWrapper->getType())) {
                continue;
            }

            $expectedName = $this->getExpectedNameFromType($propertyWrapper->getType());
            if ($oldName === $expectedName) {
                continue;
            }

            $propertyWrapper->changeName($expectedName);

            $changedPropertyNames[$oldName] = $expectedName;
        }

        foreach ($changedPropertyNames as $oldName => $newName) {
            $this->classTokenAnalyzer->renameEveryPropertyOccurrence($oldName, $newName);
        }
    }

    private function fixClassMethods(Tokens $tokens): void
    {
        foreach ($this->classTokenAnalyzer->getMethods() as $methodIndex => $methodToken) {
            $methodWrapper = MethodWrapper::createFromTokensAndPosition($tokens, $methodIndex);
            $changedVariableNames = [];

            /** @var ArgumentWrapper[] $arguments */
            $arguments = array_reverse($methodWrapper->getArguments());

            foreach ($arguments as $argumentWrapper) {
                if ($argumentWrapper->getType() === null || ! $argumentWrapper->isClassType()) {
                    continue;
                }

                $oldName = $argumentWrapper->getName();
                if ($this->isAllowedNameOrType($oldName, $argumentWrapper->getType())) {
                    continue;
                }

                $expectedName = $this->getExpectedNameFromType($argumentWrapper->getType());

                if ($oldName === $expectedName) {
                    continue;
                }

                $argumentWrapper->changeName($expectedName);
                $changedVariableNames[$oldName] = $expectedName;
            }

            foreach ($changedVariableNames as $oldName => $newName) {
                $methodWrapper->renameEveryVariableOccurrence($oldName, $newName);
            }
        }
    }

//
//    private function fixMethod(Tokens $tokens, int $methodIndex): void
//    {
//        $methodWrapper = MethodWrapper::createFromTokensAndPosition($tokens, $methodIndex);
//        $changedVariableNames = [];
//
//        /** @var ArgumentWrapper[] $arguments */
//        $arguments = array_reverse($methodWrapper->getArguments());
//
//        foreach ($arguments as $argumentWrapper) {
//            if ($argumentWrapper->getType() === null || ! $argumentWrapper->isClassType()) {
//                continue;
//            }
//
//            $oldName = $argumentWrapper->getName();
//            if ($this->isAllowedNameOrType($oldName, $argumentWrapper->getType())) {
//                continue;
//            }
//
//            $expectedName = $this->getExpectedNameFromType($argumentWrapper->getType());
//
//            if ($oldName === $expectedName) {
//                continue;
//            }
//
//            $argumentWrapper->changeName($expectedName);
//            $changedVariableNames[$oldName] = $expectedName;
//        }
//
//        foreach ($changedVariableNames as $oldName => $newName) {
//            $methodWrapper->renameEveryVariableOccurrence($oldName, $newName);
//        }
//    }

    private function getExpectedNameFromType(string $type): string
    {
        $rawName = $type;

        // is SomeInterface
        if (Strings::endsWith($rawName, 'Interface')) {
            $rawName = Strings::substring($rawName, 0, - strlen('Interface'));
        }

        // is ISomeClass
        if ($this->isPrefixedInterface($rawName)) {
            $rawName = Strings::substring($rawName, 1);
        }

        // is AbstractClass
        if (Strings::startsWith($rawName, 'Abstract')) {
            $rawName = Strings::substring($rawName, strlen('Abstract'));
        }

        // if all is upper-cased, it should be lower-cased
        if ($rawName === strtoupper($rawName)) {
            $rawName = strtolower($rawName);
        }

        return lcfirst($rawName);
    }

    private function isPrefixedInterface(string $rawName): bool
    {
        return strlen($rawName) > 3
            && Strings::startsWith($rawName, 'I')
            && ctype_upper($rawName[1])
            && ctype_lower($rawName[2]);
    }

    private function isAllowedNameOrType(string $name, string $type): bool
    {
        if ($this->shouldSkipClass($type)) {
            return true;
        }

        // starts with adjective, e.g. (Post $firstPost, Post $secondPost)
        $expectedName = $this->getExpectedNameFromType($type);

        return Strings::contains($name, ucfirst($expectedName)) && Strings::endsWith($name, ucfirst($expectedName));
    }

    private function shouldSkipClass(string $class): bool
    {
        $skippedClasses = array_merge(
            $this->skippedClasses,
            $this->configuration[self::EXTRA_SKIPPED_CLASSES_OPTION] ?? []
        );

        foreach ($skippedClasses as $skippedClass) {
            if (fnmatch($skippedClass, $class, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }
}
