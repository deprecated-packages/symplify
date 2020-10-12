<?php declare (strict_types=1);

namespace Symplify\EasyHydrator;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use Symplify\EasyHydrator\Exception\MissingConstructorException;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;

final class ClassConstructorValuesResolver
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

    private $typeCastersCollector;


    /**
     *
     */
    public function __construct(StringFormatConverter $stringFormatConverter, TypeCastersCollector $typeCastersCollector)
    {
        $this->stringFormatConverter = $stringFormatConverter;
        $this->typeCastersCollector = $typeCastersCollector;
    }


    /**
     * @return array<int, mixed>
     */
    public function resolve(string $class, array $data): array
    {
        $arguments = [];

        $constructorMethodReflection = $this->getConstructorMethodReflection($class);
        $parameterReflections = $constructorMethodReflection->getParameters();

        foreach ($parameterReflections as $parameterReflection) {
            $propertyKey = $parameterReflection->name;
            $value = $this->getParameterValue($propertyKey, $data);

            $arguments[] = $this->typeCastersCollector->retype($value, $parameterReflection, $this);
        }

        return $arguments;
    }

    private function getParameterValue(string $parameterName, array $data)
    {
        $underscoreParameterName = $this->stringFormatConverter->camelCaseToUnderscore($parameterName);

        return $data[$parameterName] ?? $data[$underscoreParameterName] ?? '';
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
