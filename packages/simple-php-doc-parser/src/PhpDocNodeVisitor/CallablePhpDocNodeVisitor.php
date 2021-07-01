<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\Node;

final class CallablePhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(
        callable $callable,
        private ?string $docContent = null
    ) {
        $this->callable = $callable;
    }

    /**
     * @return int|Node|null
     */
    public function enterNode(Node $node)
    {
        $callable = $this->callable;
        return $callable($node, $this->docContent);
    }
}
