<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use MyCLabs\Enum\Enum;
use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use ReflectionClass;

final class NewValueObjectFactory
{
    public function create(object $valueObject): New_ | StaticCall
    {
        $valueObjectClass = $valueObject::class;

        if ($valueObject instanceof Enum) {
            return new StaticCall(new FullyQualified($valueObjectClass), $valueObject->getKey());
        }

        $propertyValues = $this->resolvePropertyValuesFromValueObject($valueObjectClass, $valueObject);
        $args = $this->createArgs($propertyValues);

        return new New_(new FullyQualified($valueObjectClass), $args);
    }

    /**
     * @return mixed[]
     */
    private function resolvePropertyValuesFromValueObject(string $valueObjectClass, object $valueObject): array
    {
        $reflectionClass = new ReflectionClass($valueObjectClass);
        $propertyValues = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $propertyValues[] = $reflectionProperty->getValue($valueObject);
        }

        return $propertyValues;
    }

    /**
     * @param mixed[] $propertyValues
     * @return Arg[]
     */
    private function createArgs(array $propertyValues): array
    {
        $args = [];
        foreach ($propertyValues as $propertyValue) {
            if (is_object($propertyValue)) {
                $nestedValueObject = $this->create($propertyValue);
                $args[] = new Arg($nestedValueObject);
            } elseif (is_array($propertyValue)) {
                $args[] = new Arg(new Array_($this->createArgs($propertyValue)));
            } else {
                $args[] = new Arg(BuilderHelpers::normalizeValue($propertyValue));
            }
        }

        return $args;
    }
}
