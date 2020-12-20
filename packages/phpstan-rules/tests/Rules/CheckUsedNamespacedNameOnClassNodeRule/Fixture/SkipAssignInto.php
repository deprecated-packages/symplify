<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\Fixture;

use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;

final class SkipAssignInto
{
    public function process(Class_ $class)
    {
        $class->name = new Identifier('BetterNaming');
    }
}
