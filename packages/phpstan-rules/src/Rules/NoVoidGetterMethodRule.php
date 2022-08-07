<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\Astral\TypeAwareNodeFinder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\NoVoidGetterMethodRuleTest
 */
final class NoVoidGetterMethodRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Getter method must return something, not void';

    /**
     * @var array<class-string<Node>>
     */
    private const STOPPING_TYPES = [
        Return_::class,
        Yield_::class,
        // possibly unneded contract override
        Throw_::class,
        Node\Stmt\Throw_::class,
    ];

    public function __construct(
        private TypeAwareNodeFinder $typeAwareNodeFinder
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isClass()) {
            return [];
        }

        if ($node->isAbstract()) {
            return [];
        }

        if (! str_starts_with($node->name->toString(), 'get')) {
            return [];
        }

        if (! $this->isVoidReturnClassMethod($node)) {
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
    public function getData(): void
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function getData(): array
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isVoidReturnClassMethod(ClassMethod $classMethod): bool
    {
        if ($this->hasClassMethodVoidReturnType($classMethod)) {
            return true;
        }

        foreach (self::STOPPING_TYPES as $stoppingType) {
            $foundNode = $this->typeAwareNodeFinder->findFirstInstanceOf($classMethod, $stoppingType);
            if ($foundNode instanceof Node) {
                return false;
            }
        }

        return true;
    }

    private function hasClassMethodVoidReturnType(ClassMethod $classMethod): bool
    {
        if ($classMethod->returnType === null) {
            return false;
        }

        if (! $classMethod->returnType instanceof Identifier) {
            return false;
        }

        return $classMethod->returnType->toString() === 'void';
    }
}
