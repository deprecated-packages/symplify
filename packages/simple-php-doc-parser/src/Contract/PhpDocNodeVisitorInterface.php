<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser\Contract;

use PHPStan\PhpDocParser\Ast\Node;

/**
 * Inspired by https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor.php
 */
interface PhpDocNodeVisitorInterface
{
    public function beforeTraverse(Node $node): void;

    /**
     * @return int|Node|null
     */
    public function enterNode(Node $node);

    public function leaveNode(Node $node): void;

    public function afterTraverse(Node $node): void;
}
