<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedArrayDimFetchRule\Fixture;

use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;

final class SkipExternalPhpParser
{
    public function addItem(ClassMethod $classMethod, Param $param)
    {
        $classMethod->params[5] = $param;
    }
}
