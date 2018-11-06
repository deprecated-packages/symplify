<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Order;

use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\PackageBuilder\Php\TypeAnalyzer;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Transformer\FixerTransformer\ClassElementSorter;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapperFactory;
use function Safe\usort;

/**
 * Inspiration @see \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer
 */
final class PropertyOrderByComplexityFixer extends AbstractSymplifyFixer
{
    /**
     * @var string
     */
    private const RATING = 'rating';

    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    /**
     * @var TypeAnalyzer
     */
    private $typeAnalyzer;

    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    /**
     * @var DocBlockWrapperFactory
     */
    private $docBlockWrapperFactory;

    /**
     * @var ClassElementSorter
     */
    private $classElementSorter;

    public function __construct(
        ClassWrapperFactory $classWrapperFactory,
        TypeAnalyzer $typeAnalyzer,
        DocBlockFinder $docBlockFinder,
        DocBlockWrapperFactory $docBlockWrapperFactory,
        ClassElementSorter $classElementSorter
    ) {
        $this->classWrapperFactory = $classWrapperFactory;
        $this->typeAnalyzer = $typeAnalyzer;
        $this->docBlockFinder = $docBlockFinder;
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->classElementSorter = $classElementSorter;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Properties should be ordered from scalar to class types.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
final class SomeFixer
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var Type
     */
    private $service; 
    
    /**
     * @var int
     */
    private $price; 
} 
CODE_SAMPLE
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
final class SomeFixer
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var int
     */
    private $price; 

    /**
     * @var Type
     */
    private $service; 
}
CODE_SAMPLE
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_VARIABLE]);
    }

    public function fix(SplFileInfo $splFileInfo, Tokens $tokens): void
    {
        for ($i = 1; $i < $tokens->count(); ++$i) {
            if (! $tokens[$i]->isClassy()) {
                continue;
            }

            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $i);

            $propertyElements = $classWrapper->getPropertyElements();

            foreach ($propertyElements as $key => $propertyElement) {
                $propertyElements[$key][self::RATING] = $this->resolveRatingFromDocType($tokens, $propertyElement);
            }

            if ($this->shouldSkip($propertyElements)) {
                continue;
            }

            $sortedPropertyElements = $this->sortPropertyWrappers($propertyElements);

            $this->classElementSorter->apply($tokens, $propertyElements, $sortedPropertyElements);
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(OrderedClassElementsFixer::class);
    }

    /**
     * @param mixed[] $propertyElement
     */
    private function resolveRatingFromDocType(Tokens $tokens, array $propertyElement): ?int
    {
        $docBlockPosition = $this->docBlockFinder->findPreviousPosition($tokens, $propertyElement['start'] + 1);

        if ($docBlockPosition === null) {
            return null;
        }

        $docBlockWrapper = $this->docBlockWrapperFactory->create(
            $tokens,
            $docBlockPosition,
            $tokens[$docBlockPosition]->getContent()
        );

        return $this->getTypeRating($docBlockWrapper->getVarTypes());
    }

    /**
     * @param mixed[] $propertyElements
     */
    private function shouldSkip(array $propertyElements): bool
    {
        $lastRating = null;
        foreach ($propertyElements as $propertyElement) {
            if ($lastRating && $lastRating > $propertyElement[self::RATING]) {
                return false;
            }

            $lastRating = $propertyElement[self::RATING];
        }

        return true;
    }

    /**
     * @param mixed[] $propertyElements
     * @return mixed[]
     */
    private function sortPropertyWrappers(array $propertyElements): array
    {
        usort($propertyElements, function (array $firstPropertyElement, array $secondPropertyElement) {
            return $firstPropertyElement['rating'] <=> $secondPropertyElement['rating'];
        });

        return $propertyElements;
    }

    /**
     * @param string[] $types
     */
    private function getTypeRating(array $types): int
    {
        $rating = 0;
        foreach ($types as $type) {
            if ($this->typeAnalyzer->isPhpReservedType($type)) {
                $rating = max($rating, 1);
            } elseif ($this->typeAnalyzer->isIterableType($type)) {
                $rating = max($rating, 2);
            } elseif (ctype_upper($type[0])) {
                // probably class type
                $rating = max($rating, 3);
                continue;
            }
        }

        return $rating;
    }
}
