<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;

class DoubleShot
{
    public function run(Node\Arg $arg, Node\Param $param)
    {
        $this->isCheck($arg, $param);
    }
    private function isCheck(\PhpParser\Node $arg, \PhpParser\Node $param)
    {
    }
}
