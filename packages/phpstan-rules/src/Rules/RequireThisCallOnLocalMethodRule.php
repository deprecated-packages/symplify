<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule\RequireThisCallOnLocalMethodRuleTest
 */
final class RequireThisCallOnLocalMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "$this-><method>()" instead of "self::<method>()" to call local method';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    public function __construct(SimpleNameResolver $simpleNameResolver, ParentNodeFinder $parentNodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
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
        if (! $this->simpleNameResolver->isName($node->class, 'self')) {
            return [];
        }

        $classMethod = $this->getClassMethodInCurrentClass($node);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        if ($classMethod->isStatic()) {
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
    public function run()
    {
        self::execute();
    }

    private function execute()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $this->execute();
    }

    private function execute()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function getClassMethodInCurrentClass(StaticCall $staticCall): ?ClassMethod
    {
        $class = $this->parentNodeFinder->findFirstParentByType($staticCall, Class_::class);
        if ($class === null) {
            return null;
        }

        $staticCallName = $this->simpleNameResolver->getName($staticCall->name);
        if ($staticCallName === null) {
            return null;
        }

        return $class->getMethod($staticCallName);
    }
}
