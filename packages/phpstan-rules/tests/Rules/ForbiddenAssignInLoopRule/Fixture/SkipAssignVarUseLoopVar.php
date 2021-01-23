<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignVarUseLoopVar
{
    public function run()
    {
        foreach ($class->stmts as $key => $classStmt) {
            if (! $classStmt instanceof $type) {
                continue;
            }

            $class->stmts = $this->insertBefore($class->stmts, $traitUse, $key);

            return;
        }
    }
}
