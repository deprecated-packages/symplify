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
use PhpParser\Node\Stmt\Echo_;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\LattePHPStanCompiler\LinkProcessor\LinkProcessorFactory;

class LinkNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NodeValueResolver $nodeValueResolver,
        private LinkProcessorFactory $linkProcessorFactory,
    ) {
    }

    /**
     * @return Node[]|null
     */
    public function leaveNode(Node $node): ?array
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
        $targetName = ltrim($targetName, '/');

        $linkProcessor = $this->linkProcessorFactory->create($targetName);
        if (! $linkProcessor) {
            return null;
        }

        $targetParams = $linkArgs[1]->value;
        $linkParams = $targetParams instanceof Array_ ? $this->createLinkParams($targetParams) : [];

        $expressions = $linkProcessor->createLinkExpressions($targetName, $linkParams, $attributes);
        if ($expressions === []) {
            return null;
        }
        return $expressions;
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
     * @return Arg[]
     */
    private function createLinkParams(Array_ $array): array
    {
        $linkParams = [];
        foreach ($array->items as $arrayItem) {
            if (! $arrayItem instanceof ArrayItem) {
                continue;
            }

            $linkParams[] = new Arg($arrayItem);
        }

        return $linkParams;
    }
}
