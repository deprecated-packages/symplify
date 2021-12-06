<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule\Fixture;

final class DocblockVoidAssign
{
    public function run()
    {
        $value = $this->getNothing();
    }

    /**
     * @return void
     */
    public function getNothing()
    {
    }
}
