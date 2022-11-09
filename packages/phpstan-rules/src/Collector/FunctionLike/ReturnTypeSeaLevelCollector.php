<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\FunctionLike;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<ClassMethod, array{int, int, string}>>
 *
 * @see \Symplify\PHPStanRules\Rules\Explicit\ReturnTypeDeclarationSeaLevelRule
 */
final class ReturnTypeSeaLevelCollector implements Collector
{
    public function __construct(
        private readonly Standard $printerStandard
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return array{int, int, string}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        // skip magic
        if ($node->isMagic()) {
            return null;
        }

        if ($node->returnType instanceof Node) {
            $typedReturnCount = 1;
            $printedNode = '';
        } else {
            $typedReturnCount = 0;
            $printedNode = $this->printerStandard->prettyPrint([$node]);
        }

        return [$typedReturnCount, 1, $printedNode];
    }
}
