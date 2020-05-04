<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Order;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\DocBlock\DocBlockManipulator;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\ClassElementSorter;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;
use Symplify\PackageBuilder\Php\TypeAnalyzer;

/**
 * @deprecated
 * Inspiration @see \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer
 */
final class PropertyOrderByComplexityFixer extends AbstractSymplifyFixer
{
    /**
     * @var string
     */
    private const RATING = 'rating';

    /**
     * @var FixerClassWrapperFactory
     */
    private $fixerClassWrapperFactory;

    /**
     * @var TypeAnalyzer
     */
    private $typeAnalyzer;

    /**
     * @var ClassElementSorter
     */
    private $classElementSorter;

    /**
     * @var DocBlockManipulator
     */
    private $docBlockManipulator;

    public function __construct(
        FixerClassWrapperFactory $fixerClassWrapperFactory,
        TypeAnalyzer $typeAnalyzer,
        ClassElementSorter $classElementSorter,
        DocBlockManipulator $docBlockManipulator
    ) {
        $this->fixerClassWrapperFactory = $fixerClassWrapperFactory;
        $this->typeAnalyzer = $typeAnalyzer;
        $this->classElementSorter = $classElementSorter;
        $this->docBlockManipulator = $docBlockManipulator;

        trigger_error(sprintf(
            'Fixer "%s" is deprecated. Use "%s" instead',
            self::class,
            'https://github.com/rectorphp/rector/pull/3305'
        ));

        sleep(3);
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

            $classWrapper = $this->fixerClassWrapperFactory->createFromTokensArrayStartPosition($tokens, $i);

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
        $varTags = $this->docBlockManipulator->resolveVarTagsIfFound($tokens, $propertyElement['start'] + 1);
        if (count($varTags) === 0) {
            return null;
        }

        return $this->getTypeRating($varTags);
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
        usort($propertyElements, function (array $firstPropertyElement, array $secondPropertyElement): int {
            return $firstPropertyElement['rating'] <=> $secondPropertyElement['rating'];
        });

        return $propertyElements;
    }

    /**
     * @param VarTagValueNode[] $types
     */
    private function getTypeRating(array $types): int
    {
        $rating = 0;
        foreach ($types as $type) {
            // remove nullables, not relevant here
            $type = $this->normalizeTypeToStringForm($type);

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

    private function normalizeTypeToStringForm(VarTagValueNode $varTagValueNode): string
    {
        if ($varTagValueNode->type instanceof UnionTypeNode) {
            foreach ($varTagValueNode->type->types as $key => $value) {
                if ((string) $value === 'null') {
                    unset($varTagValueNode->type->types[$key]);
                }
            }
        }

        $stringVarType = (string) $varTagValueNode;

        // remove "(<inside>)"
        $stringVarType = $this->normalizeUnionType($stringVarType);

        return ltrim($stringVarType, '\\');
    }

    private function normalizeUnionType(string $type): string
    {
        $matchInside = Strings::match($type, '#^\((?<content>.*?)\)$#s');
        if (isset($matchInside['content'])) {
            $type = $matchInside['content'];
        }
        return $type;
    }
}
