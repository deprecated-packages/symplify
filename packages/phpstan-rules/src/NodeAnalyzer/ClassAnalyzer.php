<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\ClassLike;

final class ClassAnalyzer
{
    /**
     * @return string[]
     */
    public function resolveConstantNames(ClassLike $classLike): array
    {
        $constantNames = [];

        foreach ($classLike->getConstants() as $classConst) {
            $constConst = $classConst->consts[0];
            $constantNames[] = $constConst->name->toString();
        }

        return $constantNames;
    }
}
