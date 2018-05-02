<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Contract;

use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;

interface PhpDocInfoDecoratorInterface
{
    public function decorate(PhpDocInfo $phpDocInfo): void;
}
