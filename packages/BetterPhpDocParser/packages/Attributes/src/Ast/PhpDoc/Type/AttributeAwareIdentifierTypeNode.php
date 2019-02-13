<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type;

use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;

final class AttributeAwareIdentifierTypeNode extends IdentifierTypeNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;
}
