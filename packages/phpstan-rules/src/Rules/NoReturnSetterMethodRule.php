<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\NoReturnSetterMethodRuleTest
 */
final class NoReturnSetterMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Setter method cannot return anything, only set value';

    /**
     * @var string
     * @see https://regex101.com/r/IIvg8L/1
     */
    private const SETTER_START_REGEX = '#^set[A-Z]#';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    public function __construct(SimpleNameResolver $simpleNameResolver, SimpleNodeFinder $simpleNodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->simpleNodeFinder = $simpleNodeFinder;
    }

    /**
     * @return array<class-string<Node>>
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
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isClass()) {
            return [];
        }

        $classMethodName = $this->simpleNameResolver->getName($node);
        if ($classMethodName === null) {
            return [];
        }

        if ($classMethodName === 'setUp') {
            return [];
        }

        if (! Strings::match($classMethodName, self::SETTER_START_REGEX)) {
            return [];
        }

        if (! $this->hasReturnReturnFunctionLike($node)) {
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
    private $name;

    public function setName(string $name)
    {
        return 1000;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $name;

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasReturnReturnFunctionLike(ClassMethod $classMethod): bool
    {
        /** @var Return_[] $returns */
        $returns = $this->simpleNodeFinder->findByType($classMethod, Return_::class);
        foreach ($returns as $return) {
            if ($return->expr !== null) {
                return true;
            }
        }

        return $this->simpleNodeFinder->hasByTypes($classMethod, [Yield_::class]);
    }
}
