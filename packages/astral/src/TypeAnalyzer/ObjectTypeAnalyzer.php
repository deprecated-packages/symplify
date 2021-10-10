<?php

declare(strict_types=1);

namespace Symplify\Astral\TypeAnalyzer;

use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;
use Symplify\Astral\Exception\ShouldNotHappenException;

final class ObjectTypeAnalyzer
{
    /**
     * @param array<class-string> $desiredClasses
     */
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
        $this->ensureIsNotTrait($desiredClass);

        if ($type instanceof UnionType && $this->doesUnionTypeContainObjectType($type, $desiredClass)) {
            return true;
        }

        $unwrappedType = TypeCombinator::removeNull($type);
        if (! $unwrappedType instanceof ObjectType) {
            return false;
        }

        return $unwrappedType->isInstanceOf($desiredClass)
            ->yes();
    }

    private function doesUnionTypeContainObjectType(UnionType $unionType, string $desiredClass): bool
    {
        foreach ($unionType->getTypes() as $unionedType) {
            if (! $unionedType instanceof TypeWithClassName) {
                continue;
            }

            if (is_a($unionedType->getClassName(), $desiredClass, true)) {
                return true;
            }
        }

        return false;
    }

    private function ensureIsNotTrait(string $desiredClass): void
    {
        if (! trait_exists($desiredClass)) {
            return;
        }

        $message = sprintf(
            'Do not use trait "%s" as type to match, it breaks the matching. Use specific class that is in this trait',
            $desiredClass
        );

        throw new ShouldNotHappenException($message);
    }
}
