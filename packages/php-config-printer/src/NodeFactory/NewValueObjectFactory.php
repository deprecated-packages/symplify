<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use ReflectionClass;

final class NewValueObjectFactory
{
    public function create(object $valueObject): New_
    {
        $valueObjectClass = get_class($valueObject);

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
                $args[] = new Arg($resolvedNestedObject = $this->create($propertyValue));
            } elseif (is_array($propertyValue)) {
                $args[] = new Arg(new Array_($this->createArgs($propertyValue)));
            } else {
                $args[] = new Arg(BuilderHelpers::normalizeValue($propertyValue));
            }
        }

        return $args;
    }
}
