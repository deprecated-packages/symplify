<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

use PhpParser\Node\Scalar\String_;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;

final class SkipInitializationWithNull
{
    public function __construct(
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser
    ) {
    }

    public function run()
    {
        $files = null;
        $something = new String_('value');

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($something, function (\PhpParser\Node $node) use (&$files) {
            $files = 1000;
        });

        return $files;
    }
}
