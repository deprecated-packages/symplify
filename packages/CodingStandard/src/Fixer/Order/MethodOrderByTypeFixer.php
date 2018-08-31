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
            'Methods should have specific order by interface.',
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

            $matchedClassType = $this->matchClassType($classWrapper);
            if ($matchedClassType === null) {
                continue;
            }

            $i = $tokens->getNextTokenOfKind($i, ['{']);
            $elements = $this->privatesCaller->callPrivateMethod(
                $this->orderedClassElementsFixer,
                'getElements',
                $tokens,
                $i
            );

            $publicMethodElements = $this->filterPublicMethodElementsFirst($elements);
            $requiredMethodOrder = $this->configuration[self::METHOD_ORDER_BY_TYPE_OPTION][$matchedClassType];

            // A. identical order of all public methods → nothing to sort
            if (array_keys($publicMethodElements) === $requiredMethodOrder) {
                return;
            }

            $sorted = [];
            foreach ($requiredMethodOrder as $methodName) {
                $sorted[] = $publicMethodElements[$methodName];
                unset($publicMethodElements[$methodName]);
            }

            $sorted = array_merge($sorted, $publicMethodElements);

            $endIndex = $elements[count($elements) - 1]['end'];

            if ($sorted !== $elements) {
                $this->privatesCaller->callPrivateMethod(
                    $this->orderedClassElementsFixer,
                    'sortTokens',
                    $tokens,
                    $i,
                    $endIndex,
                    $sorted
                );
            }
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
        $fixerOptionBuilder = new FixerOptionBuilder(self::METHOD_ORDER_BY_TYPE_OPTION, 'Methods order by type.');

        $methodsOrderByTypeOption = $fixerOptionBuilder->setAllowedTypes(['array'])
            ->setDefault([])
            ->getOption();

        return new FixerConfigurationResolver([$methodsOrderByTypeOption]);
    }

    /**
     * @param mixed[] $elements
     * @return mixed[]
     */
    private function filterPublicMethodElementsFirst(array $elements): array
    {
        $publicMethods = [];
        $restOfMethods = [];

        foreach ($elements as $element) {
            if ($element['type'] !== 'method') {
                $restOfMethods[$element['name']] = $element;
                continue;
            }

            if ($element['visibility'] !== 'public') {
                $restOfMethods[$element['name']] = $element;
                continue;
            }

            $publicMethods[$element['name']] = $element;
        }

        return array_merge($publicMethods, $restOfMethods);
    }

    private function isClassWrapperByParentType(ClassWrapper $classWrapper, string $type): bool
    {
        if ($classWrapper->getParentClassName()) {
            if ($classWrapper->getParentClassName() === $type) {
                return true;
            }
        }

        foreach ($classWrapper->getInterfaceNames() as $interfaceName) {
            if ($interfaceName === $type) {
                return true;
            }
        }

        return false;
    }

    private function matchClassType(ClassWrapper $classWrapper): ?string
    {
        /** @var string $type */
        foreach (array_keys($this->configuration[self::METHOD_ORDER_BY_TYPE_OPTION]) as $type) {
            if ($this->isClassWrapperByParentType($classWrapper, $type)) {
                return $type;
            }
        }

        return null;
    }
}
