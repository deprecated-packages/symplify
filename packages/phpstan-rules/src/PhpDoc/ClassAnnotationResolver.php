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
    /**
     * @var SimplePhpDocParser
     */
    private $simplePhpDocParser;

    /**
     * @var ClassReflectionResolver
     */
    private $classReflectionResolver;

    /**
     * @var ClassReferencePhpDocNodeTraverser
     */
    private $classReferencePhpDocNodeTraverser;

    public function __construct(
        SimplePhpDocParser $simplePhpDocParser,
        ClassReflectionResolver $classReflectionResolver,
        ClassReferencePhpDocNodeTraverser $classReferencePhpDocNodeTraverser
    ) {
        $this->simplePhpDocParser = $simplePhpDocParser;
        $this->classReflectionResolver = $classReflectionResolver;
        $this->classReferencePhpDocNodeTraverser = $classReferencePhpDocNodeTraverser;
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
