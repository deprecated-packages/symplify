<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor;

use Nette\Application\PresenterFactory;
use PhpParser\Comment\Doc;
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
        private NodeValueResolver $nodeValueResolver,
        private PresenterFactory $presenterFactory
    ) {
    }

    public function leaveNode(Node $node): Node|array|int|null
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

        if (! $this->isMethodCallUiLink($methodCall)) {
            return null;
        }

        return $this->prepareNodes($methodCall, $node->getAttributes());
    }

    /**
     * @return Node[]|null
     */
    private function prepareNodes(MethodCall $methodCall, array $attributes): ?array
    {
        $linkArgs = $methodCall->getArgs();
        $target = $linkArgs[0]->value;

        $targetName = $this->nodeValueResolver->resolve($target, '');
        $targetName = trim($targetName, '/');
        $methodCallVar = null;
        $targetMethodNames = [];

        if (str_ends_with($targetName, '!')) {
            $targetMethodNames[] = 'handle' . ucfirst(substr($targetName, 0, -1));
            $methodCallVar = new Variable('actualClass');
        } elseif (str_contains($targetName, ':')) {
            $actionParts = explode(':', $targetName);
            $actionName = array_pop($actionParts);
            $presenterName = implode($actionParts);
            $presenterClassName = $this->presenterFactory->formatPresenterClass($presenterName);

            $presenterNameVariable = lcfirst($presenterName) . 'Presenter';
            $methodCallVar = new Variable($presenterNameVariable);

            $attributes['comments'][] = new Doc(
                '/** @var ' . $presenterClassName . ' $' . $presenterNameVariable . ' */'
            );
            $targetMethodNames = $this->createMethodNames($presenterClassName, $actionName);
        }

        if ($methodCallVar === null) {
            return null;
        }

        if ($targetMethodNames === []) {
            return null;
        }

        $targetParams = $linkArgs[1]->value;
        $linkParams = $targetParams instanceof Array_ ? $this->createLinkParams($targetParams) : [];

        $nodes = [];
        foreach ($targetMethodNames as $targetMethodName) {
            $nodes[] = new Expression(new MethodCall($methodCallVar, $targetMethodName, $linkParams), $attributes);
            $attributes = [];   // reset attributes, we want to print them only with first expression
        }
        return $nodes;
    }

    private function isMethodCallUiLink(MethodCall $methodCall): bool
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
        if (! in_array($propertyFetchName, ['uiControl', 'uiPresenter'], true)) {
            return false;
        }
        return true;
    }

    /**
     * @return string[]
     */
    private function createMethodNames(string $presenterClassName, string $actionName): array
    {
        $targetMethodNames = [];
        // both methods have to have same parameters, so we check them both if exist
        foreach (['action', 'render'] as $type) {
            $targetMethodName = $type . ucfirst($actionName);
            if (method_exists($presenterClassName, $targetMethodName)) {
                $targetMethodNames[] = $targetMethodName;
            }
        }
        return $targetMethodNames;
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
