<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser\ClassReferencePhpDocNodeTraverser;
use Symplify\PHPStanRules\PhpDocParser\SimplePhpDocParser;

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
        $phpDocNode = $this->simplePhpDocParser->parseNode($node);
        if (! $phpDocNode instanceof PhpDocNode) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $this->classReferencePhpDocNodeTraverser->decoratePhpDocNode($phpDocNode, $classReflection);

        $classAnnotations = [];
        foreach ($phpDocNode->getTags() as $phpDocTagNode) {
            $classAnnotations[] = $phpDocTagNode->name;
        }

        return $classAnnotations;
    }
}
