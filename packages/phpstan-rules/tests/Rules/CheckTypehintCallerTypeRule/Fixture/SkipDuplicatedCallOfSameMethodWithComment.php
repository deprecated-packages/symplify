<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PHPStan\Node\FileNode;
use Rector\Core\PhpParser\Node\CustomNode\FileWithoutNamespace;

final class SkipDuplicatedCallOfSameMethodWithComment
{
    public function firstMethod()
    {
        $this->printFile(new FileWithoutNamespace([]));
    }

    /**
     * @param FileWithoutNamespace|FileNode $node
     */
    private function printFile(\PhpParser\Node $node)
    {
    }

    public function secondMethod()
    {
        // some comment
        $this->printFile(new FileNode([]));
    }
}
