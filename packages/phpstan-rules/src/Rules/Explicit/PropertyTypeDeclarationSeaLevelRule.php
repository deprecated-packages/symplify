<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Collector\ClassLike\PropertyTypeSeaLevelCollector;
use Symplify\PHPStanRules\Formatter\SeaLevelRuleErrorFormatter;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\PropertyTypeDeclarationSeaLevelRule\PropertyTypeDeclarationSeaLevelRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PropertyTypeDeclarationSeaLevelRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible property types, only %d %% actually have it. Add more property types to get over %d %%';

    private float $minimalLevel = 0.80;

    private bool $printSuggestions = true;

    public function __construct(
        private SeaLevelRuleErrorFormatter $seaLevelRuleErrorFormatter,
        float $minimalLevel = 0.80,
        bool $printSuggestions = true
    ) {
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

        $printedUntypedPropertiesContents = [];

        foreach ($propertySeaLevelDataByFilePath as $propertySeaLevelData) {
            foreach ($propertySeaLevelData as $nestedPropertySeaLevelData) {
                $typedPropertyCount += $nestedPropertySeaLevelData[0];
                $propertyCount += $nestedPropertySeaLevelData[1];

                if ($this->printSuggestions === false) {
                    continue;
                }

                /** @var string $printedPropertyContent */
                $printedPropertyContent = $nestedPropertySeaLevelData[2];
                if ($printedPropertyContent !== '') {
                    $printedUntypedPropertiesContents[] = trim($printedPropertyContent);
                }
            }
        }

        return $this->seaLevelRuleErrorFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->minimalLevel,
            $propertyCount,
            $typedPropertyCount,
            $printedUntypedPropertiesContents
        );
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
