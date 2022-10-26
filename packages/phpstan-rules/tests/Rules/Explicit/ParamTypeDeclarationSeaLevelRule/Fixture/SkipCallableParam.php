<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ParamTypeDeclarationSeaLevelRule\Fixture;

final class SkipCallableParam
{
    /**
     * @param callable $callable
     */
    public function run($callable)
    {
    }
}
