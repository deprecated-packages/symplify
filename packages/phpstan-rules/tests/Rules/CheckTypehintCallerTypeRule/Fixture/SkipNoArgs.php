<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;

final class SkipNoArgs
{
    public function run()
    {
        $this->execute();
    }
}
