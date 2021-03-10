<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ErrorType;
use PHPStan\Type\ObjectType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\NodeAnalyzer\AssignAnalyzer;
use Symplify\PHPStanRules\NodeFinder\ReturnNodeFinder;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\NoModifyAndReturnSelfObjectRuleTest
 */
final class NoModifyAndReturnSelfObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use void instead of modify and return self object';

    /**
     * @var ReturnNodeFinder
     */
    private $returnNodeFinder;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var AssignAnalyzer
     */
    private $assignAnalyzer;

    public function __construct(
        ReturnNodeFinder $returnNodeFinder,
        NodeComparator $nodeComparator,
        SimpleNodeFinder $simpleNodeFinder,
        SimpleNameResolver $simpleNameResolver,
        AssignAnalyzer $assignAnalyzer
    ) {
        $this->returnNodeFinder = $returnNodeFinder;
        $this->nodeComparator = $nodeComparator;
        $this->simpleNodeFinder = $simpleNodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->assignAnalyzer = $assignAnalyzer;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Return_::class];
    }

    /**
     * @param Return_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof Variable) {
            return [];
        }

        $classMethod = $this->simpleNodeFinder->findFirstParentByType($node, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        if (! $this->isReturnedVariableParam($node->expr, $classMethod)) {
            return [];
        }

        if ($this->shouldSkipForIncompatibleReturnType($classMethod, $scope)) {
            return [];
        }

        $type = $scope->getType($node->expr);
        if (! $type instanceof ObjectType) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function modify(ComposerJson $composerJson): ComposerJson
    {
        $composerJson->addPackage('some-package');
        return $composerJson;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function modify(ComposerJson $composerJson): void
    {
        $composerJson->addPackage('some-package');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isReturnedVariableParam(Variable $variable, ClassMethod $classMethod): bool
    {
        foreach ($classMethod->params as $param) {
            if ($this->nodeComparator->areNodesEqual($param->var, $variable)) {
                return true;
            }
        }

        return false;
    }

    private function shouldSkipForIncompatibleReturnType(ClassMethod $classMethod, Scope $scope): bool
    {
        $varibleNames = [];

        $returns = $this->returnNodeFinder->findReturnsWithValues($classMethod);
        foreach ($returns as $return) {
            if (! $return->expr instanceof Variable) {
                return true;
            }

            $returnedType = $scope->getType($return->expr);
            if ($returnedType instanceof ErrorType) {
                return true;
            }

            if (! $returnedType instanceof ObjectType) {
                return true;
            }

            $varibleNames[] = $this->simpleNameResolver->getName($return->expr);
        }

        /** @var string[] $uniqueVaribleNames */
        $uniqueVaribleNames = array_unique($varibleNames);
        if (count($uniqueVaribleNames) !== 1) {
            return true;
        }

        $uniqueVaribleName = $uniqueVaribleNames[0];
        return $this->assignAnalyzer->isVarialeNameBeingAssigned($classMethod, $uniqueVaribleName);
    }
}
