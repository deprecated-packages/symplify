<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpParser;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\MagicConst;
use Symplify\Astral\Naming\SimpleNameResolver;

final class ConstExprEvaluatorFactory
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function create(): ConstExprEvaluator
    {
        $basicConstExprEvaluator = new ConstExprEvaluator(function ($node) {
            if ($node instanceof MagicConst) {
                return get_class($node);
            }

            if ($node instanceof ClassConstFetch || $node instanceof ConstFetch) {
                return get_class($node);
            }

            if ($node instanceof FuncCall && $this->simpleNameResolver->isNames($node->name, ['getcwd'])) {
                return get_class($node);
            }

            throw new ConstExprEvaluationException();
        });

        return $this->createConcatAwareConstExprEvaluator($basicConstExprEvaluator);
    }

    private function createConcatAwareConstExprEvaluator(
        ConstExprEvaluator $basicConstExprEvaluator
    ): ConstExprEvaluator {
        return new ConstExprEvaluator(function ($node) use ($basicConstExprEvaluator) {
            try {
                return $basicConstExprEvaluator->evaluateDirectly($node);
            } catch (ConstExprEvaluationException $constExprEvaluationException) {
                if ($node instanceof Concat) {
                    return $basicConstExprEvaluator->evaluateDirectly(
                        $node->left
                    ) . $basicConstExprEvaluator->evaluateDirectly($node->right);
                }
            }

            throw new ConstExprEvaluationException();
        });
    }
}
