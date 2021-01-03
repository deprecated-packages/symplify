<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;

final class NodeValueResolver
{
    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    public function __construct(ConstExprEvaluator $constExprEvaluator)
    {
        $this->constExprEvaluator = $constExprEvaluator;
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function resolve(Expr $expr)
    {
        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException $constExprEvaluationException) {
            return null;
        }
    }
}
