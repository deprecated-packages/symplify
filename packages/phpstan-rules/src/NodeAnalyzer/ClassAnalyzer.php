<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\Class_;

final class ClassAnalyzer
{
    /**
     * @return string[]
     */
    public function resolveConstantNames(Class_ $class): array
    {
        $constantNames = [];

        foreach ($class->getConstants() as $classConst) {
            $constConst = $classConst->consts[0];
            $constantNames[] = $constConst->name->toString();
        }

        return $constantNames;
    }
}
