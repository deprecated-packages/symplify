<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Exception\ShouldNotHappenException;

final class NodeValueResolver
{
    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    /**
     * @var Scope
     */
    private $currentScope;

    public function __construct()
    {
        $this->constExprEvaluator = new ConstExprEvaluator(function (Expr $expr): ?string {
            if ($expr instanceof Dir) {
                if ($this->currentScope === null) {
                    throw new ShouldNotHappenException();
                }

                $currentFile = $this->currentScope->getFile();
                return dirname($currentFile, 2);
            }

            return null;
        });
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function resolve(Expr $expr, Scope $scope)
    {
        $this->currentScope = $scope;

        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException $constExprEvaluationException) {
            return null;
        }
    }
}
