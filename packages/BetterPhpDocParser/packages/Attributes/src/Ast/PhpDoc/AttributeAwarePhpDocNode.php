<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;

final class AttributeAwarePhpDocNode extends PhpDocNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;
}
