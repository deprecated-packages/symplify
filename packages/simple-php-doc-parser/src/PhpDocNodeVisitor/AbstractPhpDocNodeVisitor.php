<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use Symplify\SimplePhpDocParser\Contract\PhpDocNodeVisitorInterface;

abstract class AbstractPhpDocNodeVisitor implements PhpDocNodeVisitorInterface
{
    public function beforeTraverse(PhpDocNode $phpDocNode): void
    {
    }

    public function enterNode(Node $node): ?Node
    {
        return null;
    }

    public function leaveNode(Node $node): void
    {
    }

    public function afterTraverse(PhpDocNode $phpDocNode): void
    {
    }
}
