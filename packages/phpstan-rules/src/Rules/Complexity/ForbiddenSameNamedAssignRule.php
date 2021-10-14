<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
use Symplify\PHPStanRules\NodeAnalyzer\AssignAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\PHPUnit\TestAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\ForbiddenSameNamedAssignRuleTest
 */
final class ForbiddenSameNamedAssignRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variables "%s" are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.';

    /**
     * @var string[]
     */
    private const ALLOWED_VARIABLE_NAMES = ['position'];

    public function __construct(
        private TestAnalyzer $testAnalyzer,
        private AssignAnalyzer $assignAnalyzer,
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$value = 1000;
$value = 2000;

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = 1000;
$anotherValue = 2000;
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class];
    }

    /**
     * @param ClassMethod|Function_ $node
     * @return string[]|RuleError[]
     */
    public function process(Node $node, Scope $scope): array
    {
        // allow in test case methods, possibly to compare reults
        if ($this->testAnalyzer->isTestClassMethod($scope, $node)) {
            return [];
        }

        $assigns = $this->findCurrentScopeAssigns($node);

        $assignsByVariableNames = $this->assignAnalyzer->resolveAssignsByVariableNames(
            $assigns,
            self::ALLOWED_VARIABLE_NAMES
        );

        $overridenVariableNames = $this->resolveOverridenVariableNames($assignsByVariableNames);
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

    /**
     * @return Assign[]
     */
    private function findCurrentScopeAssigns(ClassMethod|Function_ $functionLike): array
    {
        $assigns = [];

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($functionLike->stmts, function (
            \PhpParser\Node $node
        ) use (&$assigns) {
            // avoid nested scope with different variable names
            if ($node instanceof Closure) {
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
