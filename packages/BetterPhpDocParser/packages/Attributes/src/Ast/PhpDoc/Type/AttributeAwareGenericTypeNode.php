<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type;

use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;

final class AttributeAwareGenericTypeNode extends GenericTypeNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;
}
