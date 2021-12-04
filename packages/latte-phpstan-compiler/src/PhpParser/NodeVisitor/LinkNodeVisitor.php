<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;

/**
 * from: <code> echo \Latte\Runtime\Filters::escapeHtmlAttr($this->global->uiControl->link("doSomething!", ['a']));
 * </code>
 *
 * to: <code> $actualClass->handleDoSomething('a'); </code>
 */
final class LinkNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof Echo_) {
            return null;
        }

        $staticCall = $node->exprs[0] ?? null;
        if (! $staticCall instanceof StaticCall) {
            return null;
        }

        if (count($staticCall->getArgs()) !== 1) {
            return null;
        }

        $arg = $staticCall->getArgs()[0];

        $methodCall = $arg->value;
        if (! $methodCall instanceof MethodCall) {
            return null;
        }

        if (! $this->isMethodCallUiControlLink($methodCall)) {
            return null;
        }

        $linkArgs = $methodCall->getArgs();
        $target = $linkArgs[0]->value;

        $targetName = $this->nodeValueResolver->resolve($target, '');

        // Only handle methods can be transferred for now
        if (! str_ends_with($targetName, '!')) {
            return null;
        }

        $targetParams = $linkArgs[1]->value;
        $linkParams = $targetParams instanceof Array_ ? $this->createLinkParams($targetParams) : [];

        $targetMethodName = 'handle' . ucfirst(substr($targetName, 0, -1));
        return new Expression(new MethodCall(new Variable(
            'actualClass',
        ), $targetMethodName, $linkParams), $node->getAttributes());
    }

    private function isMethodCallUiControlLink(MethodCall $methodCall): bool
    {
        $methodName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodName !== 'link') {
            return false;
        }

        $propertyFetch = $methodCall->var;
        if (! $propertyFetch instanceof PropertyFetch) {
            return false;
        }

        $propertyFetchName = $this->simpleNameResolver->getName($propertyFetch->name);
        if ($propertyFetchName !== 'uiControl') {
            return false;
        }
        return true;
    }

    /**
     * @return Arg[]
     */
    private function createLinkParams(Array_ $targetParams): array
    {
        $linkParams = [];
        foreach ($targetParams->items as $targetParam) {
            if (! $targetParam instanceof ArrayItem) {
                continue;
            }
            $linkParams[] = new Arg($targetParam);
        }
        return $linkParams;
    }
}
