<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;

final class MethodCallArrayResolver
{
    /**
     * @var ArrayAnalyzer
     */
    private $arrayAnalyzer;

    public function __construct(ArrayAnalyzer $arrayAnalyzer)
    {
        $this->arrayAnalyzer = $arrayAnalyzer;
    }

    /**
     * @return string[]
     */
    public function resolveArrayKeysOnPosition(MethodCall $methodCall, Scope $scope, int $position): array
    {
        if (! isset($methodCall->args[$position])) {
            return [];
        }

        $secondArgValue = $methodCall->args[$position]->value;
        if (! $secondArgValue instanceof Array_) {
            return [];
        }

        return $this->arrayAnalyzer->resolveStringKeys($secondArgValue, $scope);
    }
}
