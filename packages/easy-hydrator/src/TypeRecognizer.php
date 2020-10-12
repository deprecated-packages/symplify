<?php declare (strict_types=1);

namespace Symplify\EasyHydrator;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use ReflectionParameter;

final class TypeRecognizer
{
    private $lexer;

    private $phpDocParser;


    public function __construct(Lexer $lexer, PhpDocParser $phpDocParser)
    {
        $this->lexer = $lexer;
        $this->phpDocParser = $phpDocParser;
    }


    public function getParameterType(ReflectionParameter $reflectionParameter): ?string
    {
        $parameterType = $reflectionParameter->getType();
        if ($parameterType === null) {
            return null;
        }

        if (method_exists($parameterType,'getName')) {
            return $parameterType->getName();
        }

        return (string) $parameterType;
    }


    public function isParameterOfClass(ReflectionParameter $reflectionParameter, string $class): bool
    {
        $parameterType = $this->getParameterType($reflectionParameter);

        return is_a($parameterType, $class, true);
    }


    public function getParameterClass(ReflectionParameter $reflectionParameter): ?string
    {
        $docComment = $reflectionParameter->getDeclaringFunction()
            ->getDocComment();

        if ($docComment === false) {
            return null;
        }

        $declaringClass = $reflectionParameter->getDeclaringClass();

        if ($declaringClass === null) {
            return null;
        }

        $tokens = new TokenIterator($this->lexer->tokenize($docComment));

        $docNode = $this->phpDocParser->parse($tokens);

        foreach ($docNode->getParamTagValues() as $paramTagValueNode) {
            $parameterName = Strings::after($paramTagValueNode->parameterName, '$');

            if ($parameterName !== $reflectionParameter->getName()) {
                continue;
            }

            $typeNode = $paramTagValueNode->type;

            if ($typeNode instanceof ArrayTypeNode) {
                /** @var IdentifierTypeNode $identifierTypeNode */
                $identifierTypeNode = $typeNode->type;

                return Reflection::expandClassName($identifierTypeNode->name, $declaringClass);
            }
        }

        return null;
    }
}
