<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoSetterOnServiceRule\NoSetterOnServiceRuleTest
 */
final class NoSetterOnServiceRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use setter on service';

    /**
     * @var string
     * @see https://regex101.com/r/yI3qGS/2
     */
    private const NOT_A_SERVICE_NAMESPACE_REGEX = '#\bEntity|Event|ValueObject\b#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Identifier $name */
        $namespacedName = $node->namespacedName;
        if (Strings::match($namespacedName->toString(), self::NOT_A_SERVICE_NAMESPACE_REGEX)) {
            return [];
        }

        $classMethods = $this->nodeFinder->findInstanceOf($node, ClassMethod::class);
        foreach ($classMethods as $classMethod) {
            $classMethodName = $classMethod->name->toString();
            if (! Strings::startsWith($classMethodName, 'set')) {
                continue;
            }

            $assigns = $this->nodeFinder->findInstanceOf((array) $classMethod->getStmts(), Assign::class);
            foreach ($assigns as $assign) {
                $parentVariableAssign = $assign->var->name->getAttribute('parent');
                if ($parentVariableAssign instanceof PropertyFetch || $parentVariableAssign instanceof StaticPropertyFetch) {
                    return [self::ERROR_MESSAGE];
                }
            }
        }

        return [];
    }
}
