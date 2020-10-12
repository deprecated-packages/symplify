<?php declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use ReflectionParameter;

final class ParameterTypeRecognizer
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var PhpDocParser
     */
    private $phpDocParser;

    public function __construct(Lexer $lexer, PhpDocParser $phpDocParser)
    {
        $this->lexer = $lexer;
        $this->phpDocParser = $phpDocParser;
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

        $typeNode = $this->getTypeNodeFromDoc($reflectionParameter->getName(), $docNode);

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

    public function getTypeFromTypeHint(ReflectionParameter $reflectionParameter): ?string
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

        $typeNode = $this->getTypeNodeFromDoc($reflectionParameter->getName(), $docNode);

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

    private function getDocNode(ReflectionParameter $reflectionParameter): ?PhpDocNode
    {
        $docComment = $reflectionParameter->getDeclaringFunction()
            ->getDocComment();

        if ($docComment === false) {
            return null;
        }

        $tokens = new TokenIterator($this->lexer->tokenize($docComment));

        return $this->phpDocParser->parse($tokens);
    }

    private function getTypeNodeFromDoc(string $parameterName, PhpDocNode $phpDocNode): ?TypeNode
    {
        foreach ($phpDocNode->getParamTagValues() as $paramTagValueNode) {
            $nodeParameterName = Strings::after($paramTagValueNode->parameterName, '$');

            if ($nodeParameterName !== $parameterName) {
                continue;
            }

            return $paramTagValueNode->type;
        }

        return null;
    }
}
