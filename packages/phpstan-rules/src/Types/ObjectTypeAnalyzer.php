<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Types;

use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

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

    public function isObjectOrUnionOfObjectType(Type $type, string $desiredClass): bool
    {
        $unwrappedType = $this->typeUnwrapper->unwrapNullableType($type);
        if (! $unwrappedType instanceof ObjectType) {
            return false;
        }

        return $unwrappedType->isInstanceOf($desiredClass)
            ->yes();
    }
}
