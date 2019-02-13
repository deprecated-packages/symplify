<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc;

use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;

final class AttributeAwareReturnTagValueNode extends ReturnTagValueNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;
}
