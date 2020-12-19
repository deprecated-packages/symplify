<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Types\TypeUnwrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\NoDynamicNameRuleTest
 */
final class NoDynamicNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit names over dynamic ones';

    /**
     * @var Standard
     */
    private $standard;

    /**
     * @var TypeUnwrapper
     */
    private $typeUnwrapper;

    public function __construct(Standard $standard, TypeUnwrapper $typeUnwrapper)
    {
        $this->standard = $standard;
        $this->typeUnwrapper = $typeUnwrapper;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [
            MethodCall::class,
            StaticCall::class,
            FuncCall::class,
            StaticPropertyFetch::class,
            PropertyFetch::class,
            ClassConstFetch::class,
        ];
    }

    /**
     * @param MethodCall|StaticCall|FuncCall|StaticPropertyFetch|PropertyFetch|ClassConstFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof ClassConstFetch) {
            if (! $node->class instanceof Expr) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        }

        if ($node instanceof StaticPropertyFetch) {
            if (! $node->class instanceof Expr) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        } elseif (! $node->name instanceof Expr) {
            return [];
        }

        if ($this->isClosureOrCallableType($scope, $node->name, $node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->${variable};
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->specificMethodName();
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isClosureOrCallableType(Scope $scope, Expr $expr, Node $node): bool
    {
        $nameStaticType = $scope->getType($expr);
        $nameStaticType = $this->typeUnwrapper->unwrapNullableType($nameStaticType);

        if ($nameStaticType instanceof CallableType) {
            return true;
        }

        if ($nameStaticType instanceof ObjectType && $nameStaticType->getClassName() === Closure::class) {
            return true;
        }

        return $this->isForeachedVariable($node);
    }

    private function isForeachedVariable(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        // possible closure
        $parentForeach = $this->getFirstParentByType($node, Foreach_::class);

        if ($parentForeach instanceof Foreach_) {
            $nameContent = $this->standard->prettyPrint([$node->name]);
            $foreachVar = $this->standard->prettyPrint([$parentForeach->valueVar]);
            if ($nameContent === $foreachVar) {
                return true;
            }
        }

        return false;
    }
}
