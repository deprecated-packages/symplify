<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Types\ConstantTypeAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\RequireClassTypeInClassMethodByTypeRuleTest
 */
final class RequireClassTypeInClassMethodByTypeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = "Method '%s()' can return only class types of '%s'";

    /**
     * @var array<string, array<string, string>>
     */
    private $requiredTypeInMethodByClass = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var ConstantTypeAnalyzer
     */
    private $constantTypeAnalyzer;

    /**
     * @param array<string, array<string, string>> $requiredTypeInMethodByClass
     */
    public function __construct(
        array $requiredTypeInMethodByClass,
        SimpleNameResolver $simpleNameResolver,
        NodeFinder $nodeFinder,
        ConstantTypeAnalyzer $constantTypeAnalyzer
    ) {
        $this->requiredTypeInMethodByClass = $requiredTypeInMethodByClass;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
        $this->constantTypeAnalyzer = $constantTypeAnalyzer;
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
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        if ($classReflection->isInterface()) {
            return [];
        }

        foreach ($this->requiredTypeInMethodByClass as $classType => $methodToRequiredClassString) {
            if (! is_a($classReflection->getName(), $classType, true)) {
                continue;
            }

            /** @var string $methodName */
            $methodName = key($methodToRequiredClassString);
            if (! $this->simpleNameResolver->isName($node->name, $methodName)) {
                continue;
            }

            /** @var string $requiredClassString */
            $requiredClassString = reset($methodToRequiredClassString);
            if ($this->doesReturnCorrectClassString($node, $scope, $requiredClassString)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $node->name, $requiredClassString);
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Required specific class-string types in defined methods', [

            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass implements SomeTypeInterface
{
    /**
     * @return string[]
     */
    public function someMethod(): array
    {
        return [AnyClass::class];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node\Scalar\String_;

class SomeClass implements SomeTypeInterface
{
    /**
     * @return string[]
     */
    public function someMethod(): array
    {
        return [String_::class];
    }
}
CODE_SAMPLE
                ,
                [
                    'requiredTypeInMethodByClass' => [
                        'SomeTypeInterface' => [
                            'someMethod' => Node::class,
                        ],
                    ],
                ]
            ),
        ]);
    }

    private function doesReturnCorrectClassString(
        ClassMethod $classMethod,
        Scope $scope,
        string $requiredClassString
    ): bool {
        /** @var Return_[] $returns */
        $returns = $this->nodeFinder->findInstanceOf((array) $classMethod->stmts, Return_::class);
        foreach ($returns as $return) {
            if ($return->expr === null) {
                return false;
            }

            $returnedExprType = $scope->getType($return->expr);
            if (! $this->constantTypeAnalyzer->isConstantClassStringType($returnedExprType, $requiredClassString)) {
                return false;
            }
        }

        return true;
    }
}
