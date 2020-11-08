<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
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
        if (! $node->class instanceof Name) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->class, 'parent')) {
            return [];
        }

        $classMethod = $this->resolveCurrentClassMethod($node);
        if ($classMethod === null) {
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

CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'

CODE_SAMPLE
            ),
        ]);
    }

    private function isMethodNameExistsInCurrentClass(ClassMethod $classMethod, string $methodName): bool
    {
        $class = $this->resolveCurrentClass($classMethod);
        return $class instanceof Class_ && $class->getMethod($methodName) instanceof ClassMethod;
    }
}
