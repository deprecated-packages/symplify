<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\FunctionLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\NodeAnalyzer\AssignAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PHPUnit\TestAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\ForbiddenSameNamedNewInstanceRuleTest
 */
final class ForbiddenSameNamedNewInstanceRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'New objects with "%s" name are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.';

    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private TestAnalyzer $testAnalyzer,
        private AssignAnalyzer $assignAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$product = new Product();
$product = new Product();

$this->productRepository->save($product);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$firstProduct = new Product();
$secondProduct = new Product();

$this->productRepository->save($firstProduct);
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return string[]|RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // allow in test case methods, possibly to compare reults
        if ($this->testAnalyzer->isTestClassMethod($scope, $node)) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->simpleNodeFinder->findByType($node, Assign::class);

        $exclusivelyNewAssignsByVariableNames = $this->assignAnalyzer->resolveExclusivelyNewAssignsByVariableNames(
            $assigns
        );

        $overridenVariableNames = $this->resolveOverridenVariableNames($exclusivelyNewAssignsByVariableNames);
        if ($overridenVariableNames === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, implode('", "', $overridenVariableNames));
        return [$errorMessage];
    }

    /**
     * @param array<string, Assign[]> $assignsByVariableNames
     * @return string[]
     */
    private function resolveOverridenVariableNames(array $assignsByVariableNames): array
    {
        $overriddenVariableNames = [];
        foreach ($assignsByVariableNames as $variableName => $assigns) {
            if (count($assigns) < 2) {
                continue;
            }

            $overriddenVariableNames[] = $variableName;
        }

        return $overriddenVariableNames;
    }
}
