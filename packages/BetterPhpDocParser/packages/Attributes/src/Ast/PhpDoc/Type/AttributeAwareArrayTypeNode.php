<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;

final class AttributeAwareArrayTypeNode extends ArrayTypeNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;
}
