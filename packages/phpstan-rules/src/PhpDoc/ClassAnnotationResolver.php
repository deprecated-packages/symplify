<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\PhpDocParser\SimplePhpDocParser;
use Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser\ClassReferencePhpDocNodeTraverser;

final class ClassAnnotationResolver
{
    public function __construct(
        private SimplePhpDocParser $simplePhpDocParser,
        private ClassReferencePhpDocNodeTraverser $classReferencePhpDocNodeTraverser
    ) {
    }

    /**
     * @api
     * @return string[]
     */
    public function resolveClassAnnotations(Node $node, Scope $scope): array
    {
        $simplePhpDocNode = $this->simplePhpDocParser->parseNode($node);
        if (! $simplePhpDocNode instanceof PhpDocNode) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
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
