<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attribute;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Symplify\BetterPhpDocParser\Contract\PhpDocParser\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\Exception\ShouldNotHappenException;
use Symplify\BetterPhpDocParser\PhpDocNodeInfo;
use Symplify\BetterPhpDocParser\PhpDocParser\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Symplify\BetterPhpDocParser\PhpDocParser\Ast\PhpDoc\AttributeAwarePhpDocTextNode;

final class AttributeAwarePhpDocFactory
{
    public function createFromNodeStartAndEnd(Node $node, int $tokenStart, int $tokenEnd): AttributeAwareNodeInterface
    {
        if ($node instanceof PhpDocTagNode) {
            $node = new AttributeAwarePhpDocTagNode($node->name, $node->value);
        } elseif ($node instanceof PhpDocTextNode) {
            $node = new AttributeAwarePhpDocTextNode($node->text);
        } else {
            throw new ShouldNotHappenException(sprintf(
                'Todo implement attribute conversion for "%s" in "%s"',
                get_class($node),
                __METHOD__
            ));
        }

        $node->setAttribute(Attribute::PHP_DOC_NODE_INFO, new PhpDocNodeInfo($tokenStart, $tokenEnd));

        return $node;
    }
}
