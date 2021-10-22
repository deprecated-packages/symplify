<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\LattePHPStanCompiler\Latte\Filters\FilterMatcher;
use Symplify\LattePHPStanCompiler\ValueObject\FunctionCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\NonStaticCallReference;
use Symplify\LattePHPStanCompiler\ValueObject\StaticCallReference;

/**
 * Make \Latte\Runtime\Defaults::getFilters() explicit, from: $this->filters->{magic}(...)
 *
 * to: \Latte\Runtime\Filters::date(...)
 */
final class MagicFilterToExplicitCallNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private FilterMatcher $filterMatcher
    ) {
    }

    /**
     * Looking for: "$this->filters->{magic}"
     */
    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if (! $node->name instanceof Expr) {
            return null;
        }

        $dynamicName = $node->name;
        if (! $dynamicName instanceof PropertyFetch) {
            return null;
        }

        if (! $this->isPropertyFetchNames($dynamicName->var, 'this', 'filters')) {
            return null;
        }

        $filterName = $this->simpleNameResolver->getName($dynamicName->name);
        if ($filterName === null) {
            return null;
        }

        $callReference = $this->filterMatcher->match($filterName);

        if ($callReference instanceof StaticCallReference) {
            return new StaticCall(
                new FullyQualified($callReference->getClass()),
                new Identifier($callReference->getMethod()),
                $node->args
            );
        }

        if ($callReference instanceof NonStaticCallReference) {
            $className = $callReference->getClass();
            $variableName = lcfirst((string) Strings::after($className, '\\', -1));
            $methodCall = new MethodCall(
                new Variable($variableName),
                new Identifier($callReference->getMethod()),
                $node->args
            );
            // trying to add php doc where type of filter variable is defined
            $methodCall->setDocComment(new Doc('/** @var ' . $className . ' ' . $variableName . ' */'));
            return $methodCall;
        }

        if ($callReference instanceof FunctionCallReference) {
            return new FuncCall(new FullyQualified($callReference->getFunction()), $node->args);
        }

        return null;
    }

    private function isPropertyFetchNames(Expr $expr, string $variableName, string $propertyName): bool
    {
        if (! $expr instanceof PropertyFetch) {
            return false;
        }

        if (! $this->simpleNameResolver->isName($expr->var, $variableName)) {
            return false;
        }

        return $this->simpleNameResolver->isName($expr->name, $propertyName);
    }
}
