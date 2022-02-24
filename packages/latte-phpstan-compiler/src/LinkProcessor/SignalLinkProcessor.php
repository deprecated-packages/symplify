<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\LinkProcessor;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Symplify\LattePHPStanCompiler\Contract\LinkProcessorInterface;

/**
 * from: <code> echo \Latte\Runtime\Filters::escapeHtmlAttr($this->global->uiControl->link("doSomething!", ['a']));
 * </code>
 *
 * to: <code> $actualClass->handleDoSomething('a'); </code>
 */
final class SignalLinkProcessor implements LinkProcessorInterface
{
    public function check(string $targetName): bool
    {
        return str_ends_with($targetName, '!');
    }

    /**
     * @param Arg[] $linkParams
     * @param array<string, mixed> $attributes
     * @return Expression[]
     */
    public function createLinkExpressions(string $targetName, array $linkParams, array $attributes): array
    {
        $variable = new Variable('actualClass');
        $methodName = 'handle' . ucfirst(substr($targetName, 0, -1));
        return [new Expression(new MethodCall($variable, $methodName, $linkParams), $attributes)];
    }
}
