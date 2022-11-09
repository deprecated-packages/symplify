<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Collector\ClassLike\PropertyTypeSeaLevelCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\PropertyTypeDeclarationSeaLevelRule\PropertyTypeDeclarationSeaLevelRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PropertyTypeDeclarationSeaLevelRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible property types, only %d %% actually have it. Add more property types to get over %d %%';
    /**
     * @var float
     */
    private $minimalLevel = 0.80;

    /**
     * @var bool
     */
    private $printSuggestions = true;

    public function __construct(float $minimalLevel = 0.80, bool $printSuggestions = true)
    {
        $this->minimalLevel = $minimalLevel;
        $this->printSuggestions = $printSuggestions;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $propertySeaLevelDataByFilePath = $node->get(PropertyTypeSeaLevelCollector::class);

        $typedPropertyCount = 0;
        $propertyCount = 0;

        $printedUntypedPropertiesContents = '';

        foreach ($propertySeaLevelDataByFilePath as $propertySeaLevelData) {
            foreach ($propertySeaLevelData as $nestedPropertySeaLevelData) {
                $typedPropertyCount += $nestedPropertySeaLevelData[0];
                $propertyCount += $nestedPropertySeaLevelData[1];

                /** @var string $printedPropertyContent */
                $printedPropertyContent = $nestedPropertySeaLevelData[2];
                if ($printedPropertyContent !== '') {
                    $printedUntypedPropertiesContents .= PHP_EOL . PHP_EOL . trim($printedPropertyContent);
                }
            }
        }

        if ($propertyCount === 0) {
            return [];
        }

        $propertyTypeDeclarationSeaLevel = $typedPropertyCount / $propertyCount;

        // has the code met the minimal sea level of types?
        if ($propertyTypeDeclarationSeaLevel >= $this->minimalLevel) {
            return [];
        }

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $propertyCount,
            $propertyTypeDeclarationSeaLevel * 100,
            $this->minimalLevel * 100
        );

        if ($this->printSuggestions) {
            $errorMessage .= $printedUntypedPropertiesContents . PHP_EOL;

            // keep error printable
            $errorMessage = Strings::truncate($errorMessage, 8000);
        }

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public $name;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public string $name;
}
CODE_SAMPLE
            ),
        ]);
    }
}
