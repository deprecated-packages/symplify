<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Collector\FunctionLike\ReturnTypeSeaLevelCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ReturnTypeDeclarationSeaLevelRule\ReturnTypeDeclarationSeaLevelRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class ReturnTypeDeclarationSeaLevelRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible return types, only %d %% actually have it. Add more return types to get over %d %%';

    public function __construct(
        private float $minimalLevel = 0.80
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
        $returnSeaLevelDataByFilePath = $node->get(ReturnTypeSeaLevelCollector::class);

        $typedReturnCount = 0;
        $returnCount = 0;

        $printedClassMethods = '';

        foreach ($returnSeaLevelDataByFilePath as $returnSeaLevelData) {
            foreach ($returnSeaLevelData as $nestedReturnSeaLevelData) {
                $typedReturnCount += $nestedReturnSeaLevelData[0];
                $returnCount += $nestedReturnSeaLevelData[1];

                /** @var string $printedClassMethod */
                $printedClassMethod = $nestedReturnSeaLevelData[2];
                if ($printedClassMethod !== '') {
                    $printedClassMethods .= PHP_EOL . PHP_EOL . trim($printedClassMethod);
                }
            }
        }

        if ($returnCount === 0) {
            return [];
        }

        $returnTypeDeclarationSeaLevel = $typedReturnCount / $returnCount;

        // has the code met the minimal sea level of types?
        if ($returnTypeDeclarationSeaLevel >= $this->minimalLevel) {
            return [];
        }

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $returnCount,
            $returnTypeDeclarationSeaLevel * 100,
            $this->minimalLevel * 100
        );

        $errorMessage .= $printedClassMethods . PHP_EOL;

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
