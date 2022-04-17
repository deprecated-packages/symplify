<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
use Symplify\PHPStanRules\NodeAnalyzer\AssignAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PHPUnit\TestAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\ForbiddenSameNamedAssignRuleTest
 */
final class ForbiddenSameNamedAssignRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variables "%s" are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.';

    /**
     * @param string[] $allowedVariableNames
     */
    public function __construct(
        private TestAnalyzer $testAnalyzer,
        private AssignAnalyzer $assignAnalyzer,
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser,
        private array $allowedVariableNames
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$value = 1000;
$value = 2000;

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = 1000;
$anotherValue = 2000;
CODE_SAMPLE
                ,
                [
                    'allowedVariableNames' => ['position'],
                ]
            ),
        ]);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof ClassMethod && ! $node instanceof Function_) {
            return [];
        }

        // allow in test case methods, possibly to compare reults
        if ($this->testAnalyzer->isTestClassMethod($scope, $node)) {
            return [];
        }

        $assigns = $this->findCurrentScopeAssigns($node);

        $assignsByVariableNames = $this->assignAnalyzer->resolveAssignsByVariableNames(
            $assigns,
            $this->allowedVariableNames
        );

        $overriddenVariableNames = $this->resolveOverriddenVariableNames($assignsByVariableNames);
        if ($overriddenVariableNames === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, implode('", "', $overriddenVariableNames));
        return [$errorMessage];
    }

    /**
     * @param array<string, Assign[]> $assignsByVariableNames
     * @return string[]
     */
    private function resolveOverriddenVariableNames(array $assignsByVariableNames): array
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

    /**
     * @return Assign[]
     */
    private function findCurrentScopeAssigns(ClassMethod|Function_ $functionLike): array
    {
        $assigns = [];

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($functionLike->stmts, function (
            Node $node
        ) use (&$assigns): int|null {
            // avoid nested scope with different variable names
            if ($node instanceof Closure) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            // skip switch branches
            if ($node instanceof Switch_) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            if ($node instanceof BooleanOr) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            if (! $node instanceof Assign) {
                return null;
            }

            $assigns[] = $node;
            return null;
        });

        return $assigns;
    }
}
