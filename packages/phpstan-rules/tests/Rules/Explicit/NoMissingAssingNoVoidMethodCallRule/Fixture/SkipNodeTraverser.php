<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use PhpParser\NodeTraverser;

final class SkipNodeTraverser
{
    public function run()
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->traverse([]);
    }
}
