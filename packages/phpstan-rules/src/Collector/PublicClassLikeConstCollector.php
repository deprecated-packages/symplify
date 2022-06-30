<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;

/**
 * @implements Collector<ClassConst, array<array{class-string, string, int}>>
 */
final class PublicClassLikeConstCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassConst::class;
    }

    /**
     * @param ClassConst $node
     * @return array<array{class-string, string, int}>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if (! $node->isPublic()) {
            return null;
        }

        if ($this->isApiDoc($node)) {
            return null;
        }

        $constantNames = [];
        foreach ($node->consts as $constConst) {
            $constantNames[] = [$classReflection->getName(), $constConst->name->toString(), $node->getLine()];
        }

        return $constantNames;
    }

    private function isApiDoc(ClassConst $classConst): bool
    {
        $docComment = $classConst->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return str_contains($docComment->getText(), '@api');
    }
}
