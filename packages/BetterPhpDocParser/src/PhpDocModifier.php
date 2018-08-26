<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;

final class PhpDocModifier
{
    public function removeTagByName(PhpDocInfo $phpDocInfo, string $tagName): void
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        $phpDocTagNodes = $phpDocNode->getTagsByName('@' . ltrim($tagName, '@'));

        foreach ($phpDocTagNodes as $phpDocTagNode) {
            $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
        }
    }

    public function removeTagByNameAndContent(PhpDocInfo $phpDocInfo, string $tagName, string $tagContent): void
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        $phpDocTagNodes = $phpDocNode->getTagsByName('@' . ltrim($tagName, '@'));

        foreach ($phpDocTagNodes as $phpDocTagNode) {
            if (! $phpDocTagNode instanceof PhpDocTagNode) {
                continue;
            }

            if (! $phpDocTagNode->value instanceof PhpDocTagValueNode) {
                continue;
            }

            // e.g. @method someMethod(), only matching content is enough, due to real case usability
            if (Strings::contains((string) $phpDocTagNode->value, $tagContent)) {
                $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
            }
        }
    }

    public function removeParamTagByParameter(PhpDocInfo $phpDocInfo, string $parameterName): void
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        /** @var PhpDocTagNode[] $phpDocTagNodes */
        $phpDocTagNodes = $phpDocNode->getTagsByName('@param');

        foreach ($phpDocTagNodes as $phpDocTagNode) {
            /** @var ParamTagValueNode|InvalidTagValueNode $paramTagValueNode */
            $paramTagValueNode = $phpDocTagNode->value;

            $parameterName = '$' . ltrim($parameterName, '$');

            // process invalid tag values
            if ($paramTagValueNode instanceof InvalidTagValueNode) {
                if ($paramTagValueNode->value === $parameterName) {
                    $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
                }
                // process normal tag
            } elseif ($paramTagValueNode->parameterName === $parameterName) {
                $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
            }
        }
    }

    public function removeReturnTagFromPhpDocNode(PhpDocNode $phpDocNode): void
    {
        foreach ($phpDocNode->getReturnTagValues() as $returnTagValue) {
            $this->removeTagFromPhpDocNode($phpDocNode, $returnTagValue);
        }
    }

    /**
     * @param PhpDocTagNode|PhpDocTagValueNode $phpDocTagOrPhpDocTagValueNode
     */
    public function removeTagFromPhpDocNode(PhpDocNode $phpDocNode, $phpDocTagOrPhpDocTagValueNode): void
    {
        // remove specific tag
        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            if ($phpDocChildNode === $phpDocTagOrPhpDocTagValueNode) {
                unset($phpDocNode->children[$key]);
                return;
            }
        }

        // or by type
        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            if ($phpDocChildNode->value === $phpDocTagOrPhpDocTagValueNode) {
                unset($phpDocNode->children[$key]);
            }
        }
    }

    public function replaceTagByAnother(PhpDocNode $phpDocNode, string $oldTag, string $newTag): void
    {
        $oldTag = '@' . ltrim($oldTag, '@');
        $newTag = '@' . ltrim($newTag, '@');

        foreach ($phpDocNode->children as $phpDocChildNode) {
            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            if ($phpDocChildNode->name === $oldTag) {
                $phpDocChildNode->name = $newTag;
            }
        }
    }

    public function replacePhpDocTypeByAnother(PhpDocNode $phpDocNode, string $oldType, string $newType): void
    {
        foreach ($phpDocNode->children as $phpDocChildNode) {
            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            if (! $this->isTagValueNodeWithType($phpDocChildNode)) {
                continue;
            }

            $phpDocChildNode->value->type = $this->replaceTypeNode($phpDocChildNode->value->type, $oldType, $newType);
        }
    }

    private function isTagValueNodeWithType(PhpDocTagNode $phpDocTagNode): bool
    {
        return $phpDocTagNode->value instanceof ParamTagValueNode ||
            $phpDocTagNode->value instanceof VarTagValueNode ||
            $phpDocTagNode->value instanceof ReturnTagValueNode;
    }

    private function replaceTypeNode(TypeNode $typeNode, string $oldType, string $newType): TypeNode
    {
        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $key => $subTypeNode) {
                $typeNode->types[$key] = $this->replaceTypeNode($subTypeNode, $oldType, $newType);
            }

            return $typeNode;
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            if (is_a($typeNode->name, $oldType, true) || $typeNode->name === $oldType) {
                return new IdentifierTypeNode($newType);
            }
        }

        return $typeNode;
    }
}
