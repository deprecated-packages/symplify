<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\PhpDoc\BarePhpDocParser;

final class NetteInjectAnalyzer
{
    /**
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        BarePhpDocParser $barePhpDocParser,
        ReflectionProvider $reflectionProvider,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->barePhpDocParser = $barePhpDocParser;
        $this->reflectionProvider = $reflectionProvider;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function isInjectPropertyFetch(PropertyFetch $propertyFetch, Scope $scope): bool
    {
        $propertyFetchVarType = $scope->getType($propertyFetch->var);
        if (! $propertyFetchVarType instanceof TypeWithClassName) {
            return false;
        }

        $className = $propertyFetchVarType->getClassName();
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $propertyName = $this->simpleNameResolver->getName($propertyFetch->name);
        if ($propertyName === null) {
            return false;
        }

        if (! $classReflection->hasProperty($propertyName)) {
            return false;
        }

        $propertyReflection = $classReflection->getProperty($propertyName, $scope);
        return $this->hasPropertyReflectionInjectAnnotation($propertyReflection);
    }

    public function isInjectProperty(Property $property): bool
    {
        if (! $property->isPublic()) {
            return false;
        }

        return $this->hasNodeInjectAnnotation($property);
    }

    public function isInjectClassMethod(ClassMethod $classMethod): bool
    {
        if (! $classMethod->isPublic()) {
            return false;
        }

        $methodName = $classMethod->name->toString();
        if (Strings::startsWith($methodName, 'inject')) {
            return true;
        }

        return $this->hasNodeInjectAnnotation($classMethod);
    }

    private function hasPropertyReflectionInjectAnnotation(PropertyReflection $propertyReflection): bool
    {
        $docComment = $propertyReflection->getDocComment();
        if ($docComment === null) {
            return false;
        }

        $phpDocTagNodes = $this->barePhpDocParser->parseDocBlockToPhpDocTagNodes($docComment);
        return $this->hasPhpDocTagNodeName($phpDocTagNodes, '@inject');
    }

    private function hasNodeInjectAnnotation(Node $node): bool
    {
        $phpDocTagNodes = $this->barePhpDocParser->parseNodeToPhpDocTagNodes($node);
        return $this->hasPhpDocTagNodeName($phpDocTagNodes, '@inject');
    }

    /**
     * @param PhpDocTagNode[] $phpDocTagNodes
     */
    private function hasPhpDocTagNodeName(array $phpDocTagNodes, string $tagName): bool
    {
        foreach ($phpDocTagNodes as $phpDocTagNode) {
            if ($phpDocTagNode->name === $tagName) {
                return true;
            }
        }

        return false;
    }
}
