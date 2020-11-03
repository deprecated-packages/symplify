<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node\Arg;
use PhpParser\Node\Param;
use PhpParser\PrettyPrinter\Standard;

final class NodeComparator
{
    /**
     * @var Standard
     */
    private $printerStandard;

    public function __construct(Standard $printerStandard)
    {
        $this->printerStandard = $printerStandard;
    }

    /**
     * @param Arg[] $methodCallArgs
     * @param Param[] $classMethodParams
     */
    public function areArgsAndParamsSame(array $methodCallArgs, array $classMethodParams): bool
    {
        if (count($methodCallArgs) !== count($classMethodParams)) {
            return false;
        }

        foreach ($methodCallArgs as $key => $arg) {
            $param = $classMethodParams[$key];

            $argContent = $this->printerStandard->prettyPrint([$arg]);
            $paramContent = $this->printerStandard->prettyPrint([$param]);

            if ($argContent === $paramContent) {
                continue;
            }

            return false;
        }

        return true;
    }
}
