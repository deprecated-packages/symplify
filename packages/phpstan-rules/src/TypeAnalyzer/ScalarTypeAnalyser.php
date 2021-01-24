<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Symplify\PackageBuilder\Php\TypeChecker;

final class ScalarTypeAnalyser
{
    /**
     * @var TypeChecker
     */
    private $typeChecker;

    public function __construct(TypeChecker $typeChecker)
    {
        $this->typeChecker = $typeChecker;
    }

    public function isScalarOrArrayType(Type $type): bool
    {
        if ($this->typeChecker->isInstanceOf(
            $type,
            [StringType::class, FloatType::class, BooleanType::class, IntegerType::class]
        )) {
            return true;
        }

        if ($type instanceof ArrayType) {
            return $this->isScalarOrArrayType($type->getItemType());
        }

        return $this->isNullableScalarType($type);
    }

    private function isNullableScalarType(Type $type): bool
    {
        if (! $type instanceof UnionType) {
            return false;
        }

        if (count($type->getTypes()) !== 2) {
            return false;
        }

        $nullSuperTypeTrinaryLogic = $type->isSuperTypeOf(new NullType());
        if (! $nullSuperTypeTrinaryLogic->yes()) {
            return false;
        }

        $unionedTypes = $type->getTypes();
        foreach ($unionedTypes as $unionedType) {
            if ($this->isScalarOrArrayType($unionedType)) {
                return true;
            }
        }

        return false;
    }
}
