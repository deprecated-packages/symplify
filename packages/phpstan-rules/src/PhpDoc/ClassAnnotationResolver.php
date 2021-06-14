<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser\ClassReferencePhpDocNodeTraverser;
use Symplify\PHPStanRules\Reflection\ClassReflectionResolver;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;

final class ClassAnnotationResolver
{
    public function __construct(
        private SimplePhpDocParser $simplePhpDocParser,
        private ClassReflectionResolver $classReflectionResolver,
        private ClassReferencePhpDocNodeTraverser $classReferencePhpDocNodeTraverser
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveClassAnnotations(Node $node, Scope $scope): array
    {
        $simplePhpDocNode = $this->simplePhpDocParser->parseNode($node);
        if (! $simplePhpDocNode instanceof PhpDocNode) {
            return [];
        }

        $classReflection = $this->classReflectionResolver->resolve($scope, $node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $this->classReferencePhpDocNodeTraverser->decoratePhpDocNode($simplePhpDocNode, $classReflection);

        $classAnnotations = [];
        foreach ($simplePhpDocNode->getTags() as $phpDocTagNode) {
            $classAnnotations[] = $phpDocTagNode->name;
        }

        return $classAnnotations;
    }
}
