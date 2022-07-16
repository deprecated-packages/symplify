<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Printer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\ClassStringType;
use PHPStan\Type\ClosureType;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Symplify\PHPStanRules\Enum\Types\ResolvedTypes;

final class CollectorMetadataPrinter
{
    private Standard $printerStandard;

    public function __construct()
    {
        $this->printerStandard = new Standard();
    }

    public function printArgTypesAsString(MethodCall $methodCall, Scope $scope): string
    {
        $stringArgTypes = [];

        foreach ($methodCall->getArgs() as $arg) {
            $argType = $scope->getType($arg->value);

            // we have no idea, nothing we can do
            if ($argType instanceof MixedType) {
                return ResolvedTypes::UNKNOWN_TYPES;
            }

            if ($argType instanceof IntersectionType) {
                return ResolvedTypes::UNKNOWN_TYPES;
            }

            if ($argType instanceof UnionType) {
                return ResolvedTypes::UNKNOWN_TYPES;
            }

            $stringArgTypes[] = $this->printTypeToString($argType);
        }

        return implode('|', $stringArgTypes);
    }

    public function printParamTypesToString(ClassMethod $classMethod): string
    {
        $printedParamTypes = [];
        foreach ($classMethod->params as $param) {
            if ($param->type === null) {
                $printedParamTypes[] = '';
                continue;
            }

            $paramType = $param->type;
            if ($paramType instanceof NullableType) {
                // unite to phpstan type
                $paramType = new UnionType([$paramType->type, new Identifier('null')]);
            }

            if ($paramType instanceof UnionType) {
                $paramType = $this->resolveSortedUnionType($paramType);
            }

            $printedParamType = $this->printerStandard->prettyPrint([$paramType]);
            $printedParamType = ltrim($printedParamType, '\\');
            $printedParamType = str_replace('|\\', '|', $printedParamType);

            $printedParamTypes[] = $printedParamType;
        }

        return implode('|', $printedParamTypes);
    }

    private function resolveSortedUnionType(UnionType $unionType): UnionType
    {
        $typeNames = [];

        foreach ($unionType->types as $type) {
            $typeNames[] = (string) $type;
        }

        sort($typeNames);

        $types = [];
        foreach ($typeNames as $typeName) {
            $types[] = new Identifier($typeName);
        }

        return new UnionType($types);
    }

    private function printTypeToString(Type $type): string
    {
        if ($type instanceof ClassStringType) {
            return 'string';
        }

        if ($type instanceof ArrayType) {
            return 'array';
        }

        if ($type instanceof BooleanType) {
            return 'bool';
        }

        if ($type instanceof IntegerRangeType) {
            return 'int';
        }

        if ($type instanceof ClosureType) {
            return 'callable';
        }

        return $type->describe(VerbosityLevel::typeOnly());
    }
}
