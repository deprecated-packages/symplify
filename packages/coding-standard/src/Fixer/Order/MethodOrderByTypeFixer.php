<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Order;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\ClassElementSorter;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\FixerClassWrapper;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;

/**
 * Inspiration @see \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer
 */
final class MethodOrderByTypeFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var string[][]
     */
    private $methodOrderByType = [];

    /**
     * @var FixerClassWrapperFactory
     */
    private $fixerClassWrapperFactory;

    /**
     * @var ClassElementSorter
     */
    private $classElementSorter;

    public function __construct(
        FixerClassWrapperFactory $fixerClassWrapperFactory,
        ClassElementSorter $classElementSorter
    ) {
        $this->fixerClassWrapperFactory = $fixerClassWrapperFactory;
        $this->classElementSorter = $classElementSorter;
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
        if ($this->methodOrderByType === []) {
            return false;
        }

        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]) && $tokens->isAnyTokenKindsFound(
            [T_IMPLEMENTS, T_EXTENDS]
        );
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->methodOrderByType === []) {
            return;
        }

        foreach ($this->getReversedClassyPositions($tokens) as $index) {
            $classWrapper = $this->fixerClassWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
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

            $sortedMethodElements = $this->sortMethodElementsAsExpected($publicMethodElements, $requiredMethodOrder);

            $this->classElementSorter->apply($tokens, $methodElements, $sortedMethodElements);
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->methodOrderByType = $configuration['method_order_by_type'] ?? [];
    }

    private function shouldSkip(FixerClassWrapper $fixerClassWrapper): bool
    {
        // we cannot check abstract classes, since they don't contain all
        if ($fixerClassWrapper->isAbstract()) {
            return true;
        }

        // no type matches
        $matchedClassType = $this->matchClassType($fixerClassWrapper);
        if ($matchedClassType === null) {
            return true;
        }

        // there are no methods to sort
        return ! $fixerClassWrapper->getMethodElements();
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

    /**
     * @return string[]
     */
    private function getRequiredMethodOrder(FixerClassWrapper $fixerClassWrapper): array
    {
        $matchedClassType = $this->matchClassType($fixerClassWrapper);

        return $this->methodOrderByType[$matchedClassType];
    }

    /**
     * @param mixed[] $methodElements
     * @param string[] $methodOrder
     * @return mixed[]
     */
    private function sortMethodElementsAsExpected(array $methodElements, array $methodOrder): array
    {
        $sorted = [];

        foreach ($methodOrder as $methodName) {
            if (isset($methodElements[$methodName])) {
                $sorted[] = $methodElements[$methodName];
                unset($methodElements[$methodName]);
            }
        }

        return array_merge($sorted, $methodElements);
    }

    private function matchClassType(FixerClassWrapper $fixerClassWrapper): ?string
    {
        $classTypesToCheck = array_keys($this->methodOrderByType);

        $matchTypes = array_intersect($fixerClassWrapper->getClassTypes(), $classTypesToCheck);
        if ($matchTypes === []) {
            return null;
        }

        // return first matching type
        return array_pop($matchTypes);
    }
}
