<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TwigPHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class TwigGetAttributeExpanderNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node): MethodCall|null
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if ($node->name instanceof Expr) {
            return null;
        }

        $functionName = $node->name->toString();
        if ($functionName !== 'twig_get_attribute') {
            return null;
        }

        // @todo match with provided type
        $variableName = $this->resolveVariableName($node);
        $getterMethodName = $this->resolveGetterName($node);

        // @todo add @var type
        // @todo complete args
        $methodCall = new MethodCall(new Variable($variableName), new Identifier($getterMethodName));

        return $methodCall;
    }

    private function resolveVariableName(FuncCall $funcCall): string
    {
        $argValue = $funcCall->args[2]->value;

        if ($argValue instanceof Coalesce) {
            $arrayDimFetch = $argValue->left;
        } elseif ($argValue instanceof ArrayDimFetch) {
            $arrayDimFetch = $argValue;
        } else {
            throw new ShouldNotHappenException();
        }

        if (! $arrayDimFetch instanceof ArrayDimFetch) {
            throw new ShouldNotHappenException();
        }

        $string = $arrayDimFetch->dim;
        if (! $string instanceof String_) {
            throw new ShouldNotHappenException();
        }

        return $string->value;
    }

    private function resolveGetterName(FuncCall $funcCall): string
    {
        $string = $funcCall->args[3]->value;
        if (! $string instanceof String_) {
            throw new ShouldNotHappenException();
        }

        return $string->value;
    }
}
