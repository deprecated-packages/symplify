<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\TypeAnalyzer\CallableTypeAnalyzer;
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
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var CallableTypeAnalyzer
     */
    private $callableTypeAnalyzer;

    public function __construct(CallableTypeAnalyzer $callableTypeAnalyzer, SimpleNameResolver $simpleNameResolver)
    {
        $this->callableTypeAnalyzer = $callableTypeAnalyzer;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return array<class-string<Node>>
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
        if ($node instanceof ClassConstFetch || $node instanceof StaticPropertyFetch) {
            if (! $node->class instanceof Expr) {
                return [];
            }

            if ($node->name instanceof Identifier && $this->simpleNameResolver->isName($node->name, 'class')) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        }

        if (! $node->name instanceof Expr) {
            return [];
        }

        if ($this->callableTypeAnalyzer->isClosureOrCallableType($scope, $node->name, $node)) {
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
}
