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
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
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
    private const NOT_A_SERVICE_NAMESPACE_REGEX = '#\bEntity|Event|ValueObject\b#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
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
        $fullyQualifiedClassName = $this->getClassName($scope);
        if ($fullyQualifiedClassName === null) {
            return [];
        }

        if (Strings::match($fullyQualifiedClassName, self::NOT_A_SERVICE_NAMESPACE_REGEX)) {
            return [];
        }

        if (! $node->isPublic()) {
            return [];
        }

        $classMethodName = $node->name->toString();
        if (! Strings::startsWith($classMethodName, 'set')) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf((array) $node->getStmts(), Assign::class);
        foreach ($assigns as $assign) {
            $assignVariable = $assign->var;
            if ($assignVariable instanceof PropertyFetch || $assignVariable instanceof StaticPropertyFetch) {
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
    public function setSomeValue(...)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeEntity
{
    public function setSomeValue(...)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
