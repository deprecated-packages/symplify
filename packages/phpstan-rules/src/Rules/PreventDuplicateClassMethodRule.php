<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\NodeFinder;
use Symplify\PHPStanRules\Printer\NodeComparator;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\PreventDuplicateClassMethodRuleTest
 */
final class PreventDuplicateClassMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Content of method "%s()" is duplicated with method in "%s" class. Use unique content or abstract service instead';

    /**
     * @var string[]
     */
    private const PHPSTAN_GET_NODE_TYPE_METHODS = ['getNodeType', 'getNodeTypes'];

    /**
     * @var string[]
     */
    private const EXCLUDED_TYPES = [Kernel::class, Extension::class];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var Standard
     */
    private $printerStandard;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    /**
     * @var array<string, string>
     */
    private $firstClassByName = [];

    /**
     * @var array<string, string>
     */
    private $contentMethodByName = [];

    public function __construct(SimpleNameResolver $simpleNameResolver, Standard $printerStandard, NodeFinder $nodeFinder, NodeComparator $nodeComparator)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->printerStandard = $printerStandard;
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return [];
        }

        if ($this->isExcludedTypes($className)) {
            return [];
        }

        if (interface_exists($className)) {
            return [];
        }

        if ($this->isConstructorOrInTestClass($node, $className)) {
            return [];
        }

        /** @var string $classMethodName */
        $classMethodName = $this->simpleNameResolver->getName($node);
        if (in_array($classMethodName, self::PHPSTAN_GET_NODE_TYPE_METHODS, true)) {
            return [];
        }

        /** @var Node[] $stmts */
        $stmts = (array) $node->stmts;
        $stmtCount = count($stmts);
        if ($stmtCount <= 1) {
            return [];
        }

        $printStmts = $this->printerStandard->prettyPrint($stmts); //$this->getPrintStmts($node, $stmts);

        if (! isset($this->contentMethodByName[$classMethodName])) {
            $this->firstClassByName[$classMethodName] = $className;
            $this->contentMethodByName[$classMethodName] = $printStmts;
            return [];
        }

        if ($printStmts !== $this->contentMethodByName[$classMethodName]) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $classMethodName, $this->firstClassByName[$classMethodName])];
    }

    /**
     * @param Node[] $stmts
     */
    private function getPrintStmts(ClassMethod $classMethod, array $stmts): string
    {
        if ($classMethod->params === []) {
            return $this->printerStandard->prettyPrint($stmts);
        }

        $maskName = 'a';
        foreach ($classMethod->params as $key => $param) {
            $paramVariable = $param->var;
            if (! $paramVariable instanceof Variable) {
                continue;
            }

            $newParamVariableName      = $maskName;
            $this->nodeFinder->find($classMethod->stmts, function (Node &$node) use ($paramVariable, $newParamVariableName) : void {
                if (! $this->nodeComparator->areNodesEqual($node, $paramVariable)) {
                    return;
                }

                $node->name = $newParamVariableName;
            });

            $param->var->name = $newParamVariableName;

            ++$maskName;
        }

        dump($this->printerStandard->prettyPrint($classMethod->stmts));

        return $this->printerStandard->prettyPrint($classMethod->stmts);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class A
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class A
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.js');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isExcludedTypes(string $className): bool
    {
        foreach (self::EXCLUDED_TYPES as $excludedType) {
            if (is_a($className, $excludedType, true)) {
                return true;
            }
        }

        return false;
    }

    private function isConstructorOrInTestClass(ClassMethod $classMethod, string $className): bool
    {
        if ($this->simpleNameResolver->isName($classMethod->name, MethodName::CONSTRUCTOR)) {
            return true;
        }

        return Strings::endsWith($className, 'Test');
    }
}
