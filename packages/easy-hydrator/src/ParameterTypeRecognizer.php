<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Nette\Utils\Reflection;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use ReflectionParameter;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;
use Symplify\SimplePhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;

/**
 * @see \Symplify\EasyHydrator\Tests\ParameterTypeRecognizerTest
 */
final class ParameterTypeRecognizer
{
    /**
     * @var SimplePhpDocParser
     */
    private $simplePhpDocParser;

    public function __construct(SimplePhpDocParser $simplePhpDocParser)
    {
        $this->simplePhpDocParser = $simplePhpDocParser;
    }

    public function isArray(ReflectionParameter $reflectionParameter): bool
    {
        $type = $this->getTypeFromTypeHint($reflectionParameter);

        if ($type === 'array') {
            return true;
        }

        $docNode = $this->getDocNode($reflectionParameter);
        if ($docNode === null) {
            return false;
        }

        $typeNode = $docNode->getParamType($reflectionParameter->getName());
        if ($typeNode instanceof ArrayTypeNode) {
            return true;
        }
        return $typeNode instanceof GenericTypeNode;
    }

    public function getType(ReflectionParameter $reflectionParameter): ?string
    {
        $type = $this->getTypeFromTypeHint($reflectionParameter);

        if ($type) {
            return $type;
        }

        return $this->getTypeFromDocBlock($reflectionParameter);
    }

    public function isParameterOfClass(ReflectionParameter $reflectionParameter, string $class): bool
    {
        $parameterType = $this->getType($reflectionParameter);

        if ($parameterType === null) {
            return false;
        }

        return is_a($parameterType, $class, true);
    }

    public function getTypeFromDocBlock(ReflectionParameter $reflectionParameter): ?string
    {
        $docNode = $this->getDocNode($reflectionParameter);

        $declaringClass = $reflectionParameter->getDeclaringClass();

        if ($declaringClass === null || $docNode === null) {
            return null;
        }

        $typeNode = $docNode->getParamType($reflectionParameter->getName());

        if ($typeNode instanceof UnionTypeNode) {
            $typeNode = $this->findFirstNonNullNodeType($typeNode);
        }

        if ($typeNode instanceof ArrayTypeNode) {
            /** @var IdentifierTypeNode $identifierTypeNode */
            $identifierTypeNode = $typeNode->type;

            return Reflection::expandClassName($identifierTypeNode->name, $declaringClass);
        }

        if ($typeNode instanceof GenericTypeNode) {
            $genericTypeNodes = $typeNode->genericTypes;
            $genericTypeNode = $genericTypeNodes[count($genericTypeNodes) - 1];

            return Reflection::expandClassName((string) $genericTypeNode, $declaringClass);
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            return Reflection::expandClassName($typeNode->name, $declaringClass);
        }

        return null;
    }

    private function getTypeFromTypeHint(ReflectionParameter $reflectionParameter): ?string
    {
        $parameterType = $reflectionParameter->getType();
        if ($parameterType === null) {
            return null;
        }

        if (method_exists($parameterType, 'getName')) {
            return $parameterType->getName();
        }

        return (string) $parameterType;
    }

    private function findFirstNonNullNodeType(UnionTypeNode $unionTypeNode): ?TypeNode
    {
        foreach ($unionTypeNode->types as $innerType) {
            if ((string) $innerType !== 'null') {
                return $innerType;
            }
        }

        return null;
    }

    private function getDocNode(ReflectionParameter $reflectionParameter): ?SimplePhpDocNode
    {
        $functionReflection = $reflectionParameter->getDeclaringFunction();
        $docComment = $functionReflection->getDocComment();

        if ($docComment === false) {
            return null;
        }

        return $this->simplePhpDocParser->parseDocBlock($docComment);
    }
}
