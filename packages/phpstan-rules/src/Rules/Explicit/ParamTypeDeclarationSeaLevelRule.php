<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Collector\FunctionLike\ParamTypeSeaLevelCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ParamTypeDeclarationSeaLevelRule\ParamTypeDeclarationSeaLevelRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class ParamTypeDeclarationSeaLevelRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible param types, only %d %% actually have it. Add more param types to get over %d %%';

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
        $paramSeaLevelDataByFilePath = $node->get(ParamTypeSeaLevelCollector::class);

        $typedParamCount = 0;
        $paramCount = 0;

        $printedClassMethods = '';

        foreach ($paramSeaLevelDataByFilePath as $paramSeaLevelData) {
            foreach ($paramSeaLevelData as $nestedParamSeaLevelData) {
                $typedParamCount += $nestedParamSeaLevelData[0];
                $paramCount += $nestedParamSeaLevelData[1];

                /** @var string $printedClassMethod */
                $printedClassMethod = $nestedParamSeaLevelData[2];
                if ($printedClassMethod !== '') {
                    $printedClassMethods .= PHP_EOL . PHP_EOL . $printedClassMethod;
                }
            }
        }

        if ($paramCount === 0) {
            return [];
        }

        $paramTypeDeclarationSeaLevel = $typedParamCount / $paramCount;

        // has the code met the minimal sea level of types?
        if ($paramTypeDeclarationSeaLevel >= $this->minimalLevel) {
            return [];
        }

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $paramCount,
            $paramTypeDeclarationSeaLevel * 100,
            $this->minimalLevel * 100
        );

        $errorMessage .= $printedClassMethods . PHP_EOL;

        // keep error printable
        $errorMessage = Strings::truncate($errorMessage, 8000);

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run($name, $age)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(string $name, int $age)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
