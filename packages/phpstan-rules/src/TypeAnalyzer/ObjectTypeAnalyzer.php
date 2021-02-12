<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;

final class ObjectTypeAnalyzer
{
    /**
     * @var TypeUnwrapper
     */
    private $typeUnwrapper;

    public function __construct(TypeUnwrapper $typeUnwrapper)
    {
        $this->typeUnwrapper = $typeUnwrapper;
    }

    public function isObjectOrUnionOfObjectTypes(Type $type, array $desiredClasses): bool
    {
        foreach ($desiredClasses as $desiredClass) {
            if ($this->isObjectOrUnionOfObjectType($type, $desiredClass)) {
                return true;
            }
        }

        return false;
    }

    public function isObjectOrUnionOfObjectType(Type $type, string $desiredClass): bool
    {
        if ($type instanceof UnionType && $this->doesUnionTypeContainObjectType($type, $desiredClass)) {
            return true;
        }

        $unwrappedType = $this->typeUnwrapper->unwrapNullableType($type);
        if (! $unwrappedType instanceof ObjectType) {
            return false;
        }

        return $unwrappedType->isInstanceOf($desiredClass)
            ->yes();
    }

    private function doesUnionTypeContainObjectType($type, string $desiredClass): bool
    {
        foreach ($type->getTypes() as $unionedType) {
            if (! $unionedType instanceof TypeWithClassName) {
                continue;
            }

            if (is_a($unionedType->getClassName(), $desiredClass, true)) {
                return true;
            }
        }

        return false;
    }
}
