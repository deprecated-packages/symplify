<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Contract;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;

interface PhpDocNodeDecoratorInterface
{
    public function decorate(PhpDocNode $phpDocNode): PhpDocNode;
}
