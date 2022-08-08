<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;

/**
 * Inspired by https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor.php
 */
interface PhpDocNodeVisitorInterface
{
    public function beforeTraverse(Node $node): void;

    public function enterNode(Node $node): int|Node|null;

    /**
     * @return null|int|\PhpParser\Node|Node[] Replacement node (or special return)
     */
    public function leaveNode(Node $node): int|\PhpParser\Node|array|null;

    public function afterTraverse(Node $node): void;
}
