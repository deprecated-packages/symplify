<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\FunctionLike;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<FunctionLike, array<int, int>>>
 */
final class ParamTypeSeaLevelCollector implements Collector
{
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return array<int, int>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // return typed params/all params
        $paramCount = count($node->getParams());
        $typedParamCount = 0;

        foreach ($node->getParams() as $param) {
            if ($param->type === null) {
                continue;
            }

            ++$typedParamCount;
        }

        return [$typedParamCount, $paramCount];
    }
}
