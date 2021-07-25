<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\While_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\RuleError;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
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

    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver,
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
        if ($this->isTestClassMethod($scope, $node)) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->simpleNodeFinder->findByType($node, Assign::class);

        $assignsByVariableNames = [];
        foreach ($assigns as $assign) {
            if (! $assign->var instanceof Variable) {
                continue;
            }

            // skip initializations
            if ($assign->expr instanceof Node\Expr\Array_) {
                continue;
            }

            if ($this->simpleNodeFinder->findFirstParentByTypes($assign, [
                For_::class, Foreach_::class, While_::class, If_::class,
            ])) {
                return [];
            }

            $variableName = $this->simpleNameResolver->getName($assign->var);
            $assignsByVariableNames[$variableName][] = $assign;
        }

        $overridenVariableNames = [];
        foreach ($assignsByVariableNames as $variableName => $assigns) {
            if (count($assigns) < 2) {
                continue;
            }

            $overridenVariableNames[] = $variableName;
        }

        if ($overridenVariableNames === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, implode('", "', $overridenVariableNames));
        return [$errorMessage];
    }

    private function isTestClassMethod(Scope $scope, ClassMethod | Function_ $node): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if (! $node instanceof ClassMethod) {
            return false;
        }

        if (! $node->isPublic()) {
            return false;
        }

        return $classReflection->isSubclassOf(TestCase::class);
    }
}
