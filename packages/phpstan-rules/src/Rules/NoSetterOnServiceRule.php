<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentGuard\ParentElementResolver\ParentMethodResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\NoSetterOnServiceRuleTest
 */
final class NoSetterOnServiceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use setter on a service';

    /**
     * @var string
     * @see https://regex101.com/r/yI3qGS/2
     */
    private const NOT_A_SERVICE_NAMESPACE_REGEX = '#\bEntity|Event|ValueObject|Collector|Provider|Kernel\b#';

    public function __construct(
        private NodeFinder $nodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private ParentMethodResolver $parentMethodResolver
    ) {
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
        if ($this->shouldSkipClass($scope)) {
            return [];
        }

        if (! $node->isPublic()) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->name, 'set*')) {
            return [];
        }

        /** @var string $methodName */
        $methodName = $this->simpleNameResolver->getName($node->name);

        $phpMethodReflection = $this->parentMethodResolver->resolve($scope, $methodName);
        if ($phpMethodReflection !== null) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf((array) $node->getStmts(), Assign::class);

        foreach ($assigns as $assign) {
            if ($assign->var instanceof PropertyFetch) {
                return [self::ERROR_MESSAGE];
            }

            if ($assign->var instanceof StaticPropertyFetch) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeService
{
    public function setSomeValue($value)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeEntity
{
    public function setSomeValue($value)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipClass(Scope $scope): bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return true;
        }

        return (bool) Strings::match($className, self::NOT_A_SERVICE_NAMESPACE_REGEX);
    }
}
