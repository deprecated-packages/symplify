<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Filters\DefaultFilterMatcher;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\StaticCallReference;

/**
 * Make \Latte\Runtime\Defaults::getFilters() explicit, from: $this->filters->{magic}(...)
 *
 * to: \Latte\Runtime\Filters::date(...)
 */
final class MagicFilterToExplicitCallNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private DefaultFilterMatcher $defaultFilterMatcher
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

        $staticCallReference = $this->defaultFilterMatcher->match($filterName);
        if (! $staticCallReference instanceof StaticCallReference) {
            return null;
        }

        $staticCall = new StaticCall(
            new FullyQualified($staticCallReference->getClass()),
            new Identifier($staticCallReference->getMethod())
        );

        $staticCall->args = $node->args;
        return $staticCall;
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
