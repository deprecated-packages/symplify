<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Name;

use Nette\Utils\Reflection;
use ReflectionClass;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareIdentifierTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;

final class FqnNameResolver
{
    /**
     * @param mixed $source
     */
    public function resolve(AttributeAwareIdentifierTypeNode $attributeAwareIdentifierTypeNode, $source): void
    {
        if ($source instanceof ReflectionClass) {
            $fqnName = Reflection::expandClassName($attributeAwareIdentifierTypeNode->name, $source);

            $attributeAwareIdentifierTypeNode->setAttribute(Attribute::FQN_NAME, $fqnName);
            return;
        }

        // $source = Node + better node finder

        die;
    }
}
