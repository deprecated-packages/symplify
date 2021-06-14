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

    public function enterNode(Node $node): ?Node
    {
        $callable = $this->callable;
        return $callable($node, $this->docContent);
    }
}
