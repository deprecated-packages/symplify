<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassLike;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements Collector<InClassNode, array<array{string[], int}>>
 */
final class TraitMethodNameCollector implements Collector
{
    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return array{string[], int}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $classLike = $node->getOriginalNode();

        if (! $classLike instanceof Class_) {
            return null;
        }

        $traitClassMethodNames = [];

        foreach ($classLike->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $trait) {
                $traitName = $trait->toString();
                if (! $this->reflectionProvider->hasClass($traitName)) {
                    continue;
                }

                $traitReflection = $this->reflectionProvider->getClass($traitName);
                $nativeTraitReflection = $traitReflection->getNativeReflection();

                foreach ($nativeTraitReflection->getMethods() as $methodReflection) {
<<<<<<< HEAD
                    $traitClassMethodNames[] = $methodReflection->getName();
=======
                    $traitClassMethodNames[] = $methodReflection->name;
>>>>>>> [PHPStanRules] Add NoDuplicatedTraitMethodNameRule
                }
            }
        }

        return [$traitClassMethodNames, $node->getLine()];
    }
}
