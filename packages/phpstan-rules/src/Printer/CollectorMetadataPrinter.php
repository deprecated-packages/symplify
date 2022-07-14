<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Printer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\VerbosityLevel;

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
            $stringArgTypes[] = $argType->describe(VerbosityLevel::typeOnly());
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

            $printedParamTypes[] = $this->printerStandard->prettyPrint([$param->type]);
        }

        return implode('|', $printedParamTypes);
    }
}
