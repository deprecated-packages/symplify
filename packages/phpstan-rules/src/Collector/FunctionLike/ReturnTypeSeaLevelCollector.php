<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\FunctionLike;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<ClassMethod, array<int, int>>>
 */
final class ReturnTypeSeaLevelCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return array<int, int>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // skip magic
        if ($node->isMagic()) {
            return [1, 1];
        }

        $typedReturnCount = $node->returnType instanceof Node ? 1 : 0;
        return [$typedReturnCount, 1];
    }
}
