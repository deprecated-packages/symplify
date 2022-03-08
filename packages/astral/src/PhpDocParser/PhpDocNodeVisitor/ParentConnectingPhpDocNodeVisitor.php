<?php

declare(strict_types=1);

namespace Symplify\Astral\PhpDocParser\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\Node;
use Symplify\Astral\PhpDocParser\ValueObject\PhpDocAttributeKey;

/**
 * @api
 *
 * Mimics https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor/ParentConnectingVisitor.php
 *
 * @see \Symplify\Astral\Tests\PhpDocParser\PhpDocNodeVisitor\ParentConnectingPhpDocNodeVisitorTest
 */
final class ParentConnectingPhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @var Node[]
     */
    private array $stack = [];

    public function beforeTraverse(Node $node): void
    {
        $this->stack = [$node];
    }

    public function enterNode(Node $node): Node
    {
        if ($this->stack !== []) {
            $parentNode = $this->stack[count($this->stack) - 1];
            $node->setAttribute(PhpDocAttributeKey::PARENT, $parentNode);
        }

        $this->stack[] = $node;

        return $node;
    }

    /**
     * @return null|int|\PhpParser\Node|Node[] Replacement node (or special return
     */
    public function leaveNode(Node $node): int|\PhpParser\Node|array|null
    {
        array_pop($this->stack);
        return null;
    }
}
