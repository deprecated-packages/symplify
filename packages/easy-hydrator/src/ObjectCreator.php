<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use Symplify\EasyHydrator\Exception\MissingConstructorException;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class ObjectCreator
{
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    /**
     * @var PhpDocParser
     */
    private $phpDocParser;

    /**
     * @var Lexer
     */
    private $lexer;

    public function __construct(StringFormatConverter $stringFormatConverter, PhpDocParser $phpDocParser, Lexer $lexer)
    {
        $this->stringFormatConverter = $stringFormatConverter;
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }


    /**
     * @return mixed
     */
    public function create(string $className, array $data)
    {
        $resolveClassConstructorValues = $this->resolveClassConstructorValues($className, $data);

        return new $className(...$resolveClassConstructorValues);
    }

    /**
     * @return array<int, mixed>
     */
    private function resolveClassConstructorValues(string $class, array $data): array
    {
        $arguments = [];

        $constructorMethodReflection = $this->getConstructorMethodReflection($class);
        $parameterReflections = $constructorMethodReflection->getParameters();

        foreach ($parameterReflections as $parameterReflection) {
            $arguments[] = $this->resolveValue($data, $parameterReflection);
        }

        return $arguments;
    }

    private function resolveValue(array $data, ReflectionParameter $reflectionParameter)
    {
        $propertyKey = $reflectionParameter->name;
        $underscoreKey = $this->stringFormatConverter->camelCaseToUnderscore($reflectionParameter->name);

        $value = $data[$propertyKey] ?? $data[$underscoreKey] ?? '';

        return $this->retypeValue($reflectionParameter, $value);
    }

    /**
     * @return bool|int|string|mixed
     */
    private function retypeValue(ReflectionParameter $reflectionParameter, $value)
    {
        if ($this->isReflectionParameterOfType($reflectionParameter, DateTimeImmutable::class)) {
            return DateTimeImmutable::createFromMutable(DateTime::from($value));
        }

        if ($this->isReflectionParameterOfType($reflectionParameter, DateTimeInterface::class)) {
            return DateTime::from($value);
        }

        $parameterType = $reflectionParameter->getType();

        if ($parameterType !== null) {
            $parameterTypeName = $parameterType->getName();

            switch ($parameterTypeName) {
                case 'string':
                    return (string) $value;
                case 'bool':
                    return (bool) $value;
                case 'int':
                    return (int) $value;
                case 'array':
                    $className = $this->getArrayParameterClass($reflectionParameter);

                    if ($className === null || ! class_exists($className)) {
                        break;
                    }

                    $objects = [];
                    foreach ($value as $itemValue) {
                        $objects[] = $this->create($className, $itemValue);
                    }
                    return $objects;
                default:
                    if (class_exists($parameterTypeName)) {
                        return $this->create($parameterTypeName, $value);
                    }
            }
        }

        return $value;
    }

    private function isReflectionParameterOfType(ReflectionParameter $reflectionParameter, string $class): bool
    {
        $parameterType = $reflectionParameter->getType();
        if ($parameterType === null) {
            return false;
        }

        /** @var ReflectionType $parameterType */
        $parameterTypeName = method_exists(
            $parameterType,
            'getName'
        ) ? $parameterType->getName() : (string) $parameterType;

        return is_a($parameterTypeName, $class, true);
    }

    private function getConstructorMethodReflection(string $class): ReflectionMethod
    {
        $reflectionClass = new ReflectionClass($class);

        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if ($constructorReflectionMethod === null) {
            throw new MissingConstructorException(sprintf('Hydrated class "%s" is missing constructor.', $class));
        }

        return $constructorReflectionMethod;
    }

    private function getArrayParameterClass(ReflectionParameter $reflectionParameter): ?string
    {
        $docComment = $reflectionParameter->getDeclaringFunction()
            ->getDocComment();

        if ($docComment === null) {
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

                return Reflection::expandClassName(
                    $identifierTypeNode->name,
                    $declaringClass
                );
            }
        }

        return null;
    }
}
