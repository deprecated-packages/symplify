<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\Node;
use Symplify\SimplePhpDocParser\ValueObject\PhpDocAttributeKey;

/**
 * Mirrors
 * https://github.com/nikic/PHP-Parser/blob/d520bc9e1d6203c35a1ba20675b79a051c821a9e/lib/PhpParser/NodeVisitor/CloningVisitor.php
 */
final class CloningPhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @return int|Node|null
     */
    public function enterNode(Node $origNode)
    {
        $node = clone $origNode;
        $node->setAttribute(PhpDocAttributeKey::ORIG_NODE, $origNode);
        return $node;
    }
}
