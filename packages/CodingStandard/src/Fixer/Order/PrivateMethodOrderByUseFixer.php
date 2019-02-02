<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Order;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\TokenRunner\Transformer\FixerTransformer\ClassElementSorter;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

final class PrivateMethodOrderByUseFixer extends AbstractSymplifyFixer
{
    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    /**
     * @var ClassElementSorter
     */
    private $classElementSorter;

    public function __construct(ClassWrapperFactory $classWrapperFactory, ClassElementSorter $classElementSorter)
    {
        $this->classWrapperFactory = $classWrapperFactory;
        $this->classElementSorter = $classElementSorter;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Private methods should be sorted by order of their call.', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]);
    }

    public function fix(SplFileInfo $splFileInfo, Tokens $tokens): void
    {
        foreach ($this->getReversedClassyPositions($tokens) as $index) {
            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);

            $desiredMethodOrder = $classWrapper->getThisMethodCallsByOrderOfAppearance();
            $privateMethodElements = $classWrapper->getPrivateMethodElements();

            $sortedPrivateMethodElements = $this->sort($privateMethodElements, $desiredMethodOrder);

            $this->classElementSorter->apply($tokens, $privateMethodElements, $sortedPrivateMethodElements);
        }
    }

    /**
     * @param mixed[] $methodElements
     * @param string[] $methodOrder
     * @return mixed[]
     */
    private function sort(array $methodElements, array $methodOrder): array
    {
        $methodOrder = array_flip($methodOrder);
        usort($methodElements, function (array $firstPropertyElement, array $secondPropertyElement) use ($methodOrder) {
            if (! isset($methodOrder[$firstPropertyElement['name']]) || ! isset($methodOrder[$secondPropertyElement['name']])) {
                return 0;
            }

            return $methodOrder[$firstPropertyElement['name']] <=> $methodOrder[$secondPropertyElement['name']];
        });

        return $methodElements;
    }
}
