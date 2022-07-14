<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Printer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ClassStringType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\VerbosityLevel;

final class CollectorMetadataPrinter
{
    private Standard $printerStandard;

    public function __construct()
    {
        $this->printerStandard = new Standard();
    }

    public function printArgTypesAsString(MethodCall $methodCall, Scope $scope): ?string
    {
        $stringArgTypes = [];

        foreach ($methodCall->getArgs() as $arg) {
            $argType = $scope->getType($arg->value);

            // we have no idea, nothing we can do
            if ($argType instanceof MixedType) {
                return null;
            }

            if ($argType instanceof ClassStringType) {
                $stringArgType = 'string';
            } elseif ($argType instanceof ArrayType) {
                $stringArgType = 'array';
            } else {
                $stringArgType = $argType->describe(VerbosityLevel::typeOnly());
            }

            $stringArgTypes[] = $stringArgType;
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

            $printedParamType = $this->printerStandard->prettyPrint([$param->type]);
            $printedParamType = ltrim($printedParamType, '\\');

            $printedParamTypes[] = $printedParamType;
        }

        return implode('|', $printedParamTypes);
    }
}
