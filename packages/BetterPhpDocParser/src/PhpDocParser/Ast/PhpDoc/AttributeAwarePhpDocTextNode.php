<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser\Ast\PhpDoc;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Symplify\BetterPhpDocParser\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Contract\PhpDocParser\Ast\AttributeAwareNodeInterface;

final class AttributeAwarePhpDocTextNode extends PhpDocTextNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;
}
