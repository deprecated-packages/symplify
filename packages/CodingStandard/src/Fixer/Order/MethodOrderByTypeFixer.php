<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Order;

use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
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
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

final class MethodOrderByTypeFixer implements DefinedFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    public const METHOD_ORDER_BY_TYPE_OPTION = 'method_order_by_type';

    /**
     * @var mixed[]
     */
    private $configuration = [];

    /**
     * @var OrderedClassElementsFixer
     */
    private $orderedClassElementsFixer;

    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    public function __construct(ClassWrapperFactory $classWrapperFactory)
    {
        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);

        $this->privatesCaller = new PrivatesCaller();
        $this->orderedClassElementsFixer = new OrderedClassElementsFixer();
        $this->classWrapperFactory = $classWrapperFactory;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Methods should have specific order by interface or parent class.',
            [
                new CodeSample(
<<<'CODE_SAMPLE'
final class SomeFixer implements FixerInterface
{
    public function isCandidate()
    {
    }
    
    public function getName()
    {
        // ...
    }
} 
CODE_SAMPLE
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]) && $tokens->isAnyTokenKindsFound(
            [T_IMPLEMENTS, T_EXTENDS]
        );
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (! $tokens[$i]->isClassy()) {
                continue;
            }

            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $i);
            if ($this->shouldSkip($classWrapper)) {
                continue;
            }

            $methodElements = $classWrapper->getMethodElements();
            $publicMethodElements = $this->filterPublicMethodsFirst($methodElements);

            $requiredMethodOrder = $this->getRequiredMethodOrder($classWrapper);

            // identical order of all public methods → nothing to sort
            if (array_keys($publicMethodElements) === $requiredMethodOrder) {
                continue;
            }

            $sorted = [];
            foreach ($requiredMethodOrder as $methodName) {
                $sorted[] = $publicMethodElements[$methodName];
                unset($publicMethodElements[$methodName]);
            }

            $sorted = array_merge($sorted, $publicMethodElements);

            // nothing to sort
            if ($sorted === $methodElements) {
                continue;
            }

            $this->privatesCaller->callPrivateMethod(
                $this->orderedClassElementsFixer,
                'sortTokens',
                $tokens,
                // first method index
                $methodElements[0]['start'] - 1,
                // last method index
                $methodElements[count($methodElements) - 1]['end'],
                $sorted
            );
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
        $fixerOptionBuilder = new FixerOptionBuilder(self::METHOD_ORDER_BY_TYPE_OPTION, 'Methods order by type.');

        $methodsOrderByTypeOption = $fixerOptionBuilder->setAllowedTypes(['array'])
            ->setDefault([])
            ->getOption();

        return new FixerConfigurationResolver([$methodsOrderByTypeOption]);
    }

    /**
     * @param mixed[] $methodElements
     * @return mixed[]
     */
    private function filterPublicMethodsFirst(array $methodElements): array
    {
        $publicMethodElements = [];
        $restOfMethods = [];

        foreach ($methodElements as $element) {
            if ($element['visibility'] === 'public') {
                $publicMethodElements[$element['name']] = $element;
            } else {
                $restOfMethods[$element['name']] = $element;
            }
        }

        return array_merge($publicMethodElements, $restOfMethods);
    }

    private function matchClassType(ClassWrapper $classWrapper): ?string
    {
        $classTypes = array_merge([$classWrapper->getParentClassName()], $classWrapper->getInterfaceNames());
        $classTypesToCheck = array_keys($this->configuration[self::METHOD_ORDER_BY_TYPE_OPTION]);

        $matchTypes = array_intersect($classTypes, $classTypesToCheck);
        if (! $matchTypes) {
            return null;
        }

        // return first matching type
        return array_pop($matchTypes);
    }

    private function shouldSkip(ClassWrapper $classWrapper): bool
    {
        // we cannot check abstract classes, since they don't contain all
        if ($classWrapper->isAbstract()) {
            return true;
        }

        // no type matches
        $matchedClassType = $this->matchClassType($classWrapper);
        if ($matchedClassType === null) {
            return true;
        }

        // there are no methods to sort
        return ! $classWrapper->getMethodElements();
    }

    /**
     * @return string[]
     */
    private function getRequiredMethodOrder(ClassWrapper $classWrapper): array
    {
        $matchedClassType = $this->matchClassType($classWrapper);

        return $this->configuration[self::METHOD_ORDER_BY_TYPE_OPTION][$matchedClassType];
    }
}
