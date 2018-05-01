<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Contract;

use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfo;

interface PhpDocInfoDecoratorInterface
{
    public function decorate(PhpDocInfo $phpDocInfo);
}
