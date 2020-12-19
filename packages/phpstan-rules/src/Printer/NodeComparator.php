<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Printer;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Param;
use PhpParser\PrettyPrinter\Standard;

final class NodeComparator
{
    /**
     * @var Standard
     */
    private $standard;

    public function __construct(Standard $standard)
    {
        $this->standard = $standard;
    }

    public function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        return $this->standard->prettyPrint([$firstNode]) === $this->standard->prettyPrint([$secondNode]);
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

            $argContent = $this->standard->prettyPrint([$arg]);
            $paramContent = $this->standard->prettyPrint([$param]);

            if ($argContent === $paramContent) {
                continue;
            }

            return false;
        }

        return true;
    }
}
