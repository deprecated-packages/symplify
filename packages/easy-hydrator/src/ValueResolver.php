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
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use Symplify\EasyHydrator\Exception\MissingConstructorException;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class ValueResolver
{
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    public function __construct(StringFormatConverter $stringFormatConverter)
    {
        $this->stringFormatConverter = $stringFormatConverter;
    }

    /**
     * @return array<int, mixed>
     */
    public function resolveClassConstructorValues(string $class, array $data): array
    {
        $arguments = [];

        $constructorMethodReflection = $this->getConstructorMethodReflection($class);
        $parameterReflections = $constructorMethodReflection->getParameters();

        foreach ($parameterReflections as $parameterReflection) {
            $arguments[] = $this->resolveValue($data, $parameterReflection, $constructorMethodReflection);
        }

        return $arguments;
    }

    private function resolveValue(
        array $data,
        ReflectionParameter $reflectionParameter,
        ReflectionMethod $reflectionMethod
    ) {
        $propertyKey = $reflectionParameter->name;
        $underscoreKey = $this->stringFormatConverter->camelCaseToUnderscore($reflectionParameter->name);

        $value = $data[$propertyKey] ?? $data[$underscoreKey] ?? '';

        return $this->retypeValue($reflectionParameter, $value, $reflectionMethod);
    }

    /**
     * @return bool|int|string|mixed
     */
    private function retypeValue(
        ReflectionParameter $reflectionParameter,
        $value,
        ReflectionMethod $reflectionMethod
    ) {
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
                // TODO: add test with generics to make sure reflection returns array
                case 'array':
                    $docBlock = $reflectionMethod->getDocComment();

                    $lexer = new Lexer();
                    $constExprParser = new ConstExprParser();
                    $phpDocParser = new PhpDocParser(new TypeParser($constExprParser), $constExprParser);

                    $tokens = new TokenIterator($lexer->tokenize($docBlock));

                    $docNode = $phpDocParser->parse($tokens);

                    $newClassName = null;

                    foreach ($docNode->getParamTagValues() as $paramTagValueNode) {
                        $parameterName = Strings::after($paramTagValueNode->parameterName, '$');

                        if ($parameterName !== $reflectionParameter->getName()) {
                            continue;
                        }

                        $typeNode = $paramTagValueNode->type;

                        if ($typeNode instanceof ArrayTypeNode) {
                            /** @var IdentifierTypeNode $identifierTypeNode */
                            $identifierTypeNode = $typeNode->type;

                            $newClassName = Reflection::expandClassName(
                                $identifierTypeNode->name,
                                $reflectionMethod->getDeclaringClass()
                            );
                        }
                    }

                    if ($newClassName === null || !class_exists($newClassName)) {
                        break;
                    }

                    $values = [];
                    foreach ($value as $sub) {
                        $resolveClassConstructorValues = $this->resolveClassConstructorValues($newClassName, $sub);

                        $values[] = new $newClassName(...$resolveClassConstructorValues);
                    }
                    return $values;
                default:
                    if (class_exists($parameterTypeName)) {
                        $resolveClassConstructorValues = $this->resolveClassConstructorValues(
                            $parameterTypeName,
                            $value
                        );

                        return new $parameterTypeName(...$resolveClassConstructorValues);
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
}
