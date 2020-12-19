<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;

class SkipMixed
{
    public function run($arg)
    {
        $this->isCheck($arg);
    }
    private function isCheck(\PhpParser\Node $arg)
    {
    }
}
