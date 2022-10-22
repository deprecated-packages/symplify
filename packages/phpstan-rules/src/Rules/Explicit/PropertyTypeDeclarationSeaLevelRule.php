<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use Nette\Utils\Arrays;
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
final class PropertyTypeDeclarationSeaLevelRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The property type sea level %d %% has not passed minimal required level of %d %%. Add more propertgy types to rise above the required level';

    public function __construct(
        private float $minimalLevel = 0.20
    ) {
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

        foreach ($propertySeaLevelDataByFilePath as $propertySeaLevelData) {
            $propertySeaLevelData = Arrays::flatten($propertySeaLevelData);

            $typedPropertyCount += $propertySeaLevelData[0];
            $propertyCount += $propertySeaLevelData[1];
        }

        if ($propertyCount === 0) {
            return [];
        }

        $propertyTypeDeclarationSeaLevel = $typedPropertyCount / $propertyCount;

        // has the code met the minimal sea level of types?
        if ($propertyTypeDeclarationSeaLevel > $this->minimalLevel) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $propertyTypeDeclarationSeaLevel * 100, $this->minimalLevel * 100);
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
