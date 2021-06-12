<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\RequireThisOnParentMethodCallRuleTest
 */
final class RequireThisOnParentMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "$this-><method>()" instead of "parent::<method>()" unless in the same named method';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->simpleNameResolver->isName($node->class, 'parent')) {
            return [];
        }

        $classMethod = $this->simpleNodeFinder->findFirstParentByType($node, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        if ($this->simpleNameResolver->areNamesEqual($classMethod->name, $node->name)) {
            return [];
        }

        /** @var string $staticCallMethodName */
        $staticCallMethodName = $this->simpleNameResolver->getName($node->name);
        if ($this->isMethodNameExistsInCurrentClass($classMethod, $staticCallMethodName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go()
    {
        parent::run();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go()
    {
        $tihs->run();
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isMethodNameExistsInCurrentClass(ClassMethod $classMethod, string $methodName): bool
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (! $class instanceof Class_) {
            return false;
        }
        return $class->getMethod($methodName) instanceof ClassMethod;
    }
}
