<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Contract;

use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePhpDocNode;

interface PhpDocNodeDecoratorInterface
{
    public function decorate(AttributeAwarePhpDocNode $phpDocNode): AttributeAwarePhpDocNode;
}
