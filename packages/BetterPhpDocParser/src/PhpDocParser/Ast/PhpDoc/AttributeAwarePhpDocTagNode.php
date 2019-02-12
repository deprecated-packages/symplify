<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser\Ast\PhpDoc;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Symplify\BetterPhpDocParser\Attribute\AttributeTrait;

final class AttributeAwarePhpDocTagNode extends PhpDocTagNode
{
    use AttributeTrait;
}

