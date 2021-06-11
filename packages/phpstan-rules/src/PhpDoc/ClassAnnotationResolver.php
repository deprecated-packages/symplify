<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\Reflection\ClassReflectionResolver;
use Symplify\SimplePhpDocParser\PhpDocNodeTraverser;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\FullyQualifyingPhpDocNodeVisitor;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;
use Symplify\SimplePhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;

final class ClassAnnotationResolver
{
    /**
     * @var SimplePhpDocParser
     */
    private $simplePhpDocParser;

    /**
     * @var FullyQualifyingPhpDocNodeVisitor
     */
    private $fullyQualifyingPhpDocNodeVisitor;

    /**
     * @var ClassReflectionResolver
     */
    private $classReflectionResolver;

    public function __construct(
        SimplePhpDocParser $simplePhpDocParser,
        FullyQualifyingPhpDocNodeVisitor $fullyQualifyingPhpDocNodeVisitor,
        ClassReflectionResolver $classReflectionResolver
    ) {
        $this->simplePhpDocParser = $simplePhpDocParser;
        $this->fullyQualifyingPhpDocNodeVisitor = $fullyQualifyingPhpDocNodeVisitor;
        $this->classReflectionResolver = $classReflectionResolver;
    }

    /**
     * @return string[]
     */
    public function resolveClassReferences(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        $classReflection = $this->classReflectionResolver->resolve($scope, $node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $refrencedClasses = [];

        $phpDocNode = $this->parseTextToPhpDocNodeWithFullyQualifiedNames($docComment->getText(), $classReflection);
        foreach ($phpDocNode->children as $docChildNode) {
            if (! $docChildNode instanceof PhpDocTagNode) {
                continue;
            }

            if (! $docChildNode->value instanceof GenericTagValueNode) {
                continue;
            }

            $genericTagValueNode = $docChildNode->value;
            $refrencedClasses = array_merge(
                $refrencedClasses,
                $genericTagValueNode->getAttribute(AttributeKey::REFERENCED_CLASSES)
            );
        }

        return $refrencedClasses;
    }

    /**
     * @return string[]
     */
    public function resolveClassAnnotations(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        $classReflection = $this->classReflectionResolver->resolve($scope, $node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $phpDocNode = $this->parseTextToPhpDocNodeWithFullyQualifiedNames($docComment->getText(), $classReflection);

        $classAnnotations = [];
        foreach ($phpDocNode->getTags() as $phpDocTagNode) {
            $classAnnotations[] = $phpDocTagNode->name;
        }

        return $classAnnotations;
    }

    private function parseTextToPhpDocNodeWithFullyQualifiedNames(
        string $docBlock,
        ClassReflection $classReflection
    ): SimplePhpDocNode {
        $simplePhpDocNode = $this->simplePhpDocParser->parseDocBlock($docBlock);

        $phpDocNodeTraverser = new PhpDocNodeTraverser();
        $this->fullyQualifyingPhpDocNodeVisitor->configureClassName($classReflection->getName());

        $phpDocNodeTraverser->addPhpDocNodeVisitor($this->fullyQualifyingPhpDocNodeVisitor);
        $phpDocNodeTraverser->traverse($simplePhpDocNode);

        return $simplePhpDocNode;
    }
}
