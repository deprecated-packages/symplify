<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassConst;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\InClassNode;
use Symplify\Astral\NodeValue\NodeValueResolver;

/**
 * @implements Collector<InClassNode, array<array{string, string, int}>>
 */
final class RegexClassConstCollector implements Collector
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return array<int, mixed[]>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();

        $collectedConstants = [];

        foreach ($classLike->getConstants() as $classConst) {
            // we need exactly one constant
            if (count($classConst->consts) !== 1) {
                continue;
            }

            $constConst = $classConst->consts[0];
            $constantName = $constConst->name->toString();

            if (! str_ends_with($constantName, '_REGEX')) {
                continue;
            }

            $resolvedValue = $this->nodeValueResolver->resolve($constConst->value, $scope->getFile());
            if (! is_string($resolvedValue)) {
                continue;
            }

            $collectedConstants[] = [$constantName, $resolvedValue, $constConst->getLine()];
        }

        return $collectedConstants;
    }
}
