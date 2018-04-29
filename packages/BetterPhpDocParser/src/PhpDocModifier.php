<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser;

use PHPStan\PhpDoc\Tag\ParamTag;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfo;

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

            // @param $paramToRemove
            if ($phpDocTagNode->value instanceof ParamTagValueNode) {
                if ((string) $phpDocTagNode->value->parameterName === '$' . ltrim($tagContent, '$')) {
                    $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
                }
            }

            // @method someMethod()
            if ((string) $phpDocTagNode->value === $tagContent) {
                $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
            }
        }
    }

    private function removeTagFromPhpDocNode(PhpDocNode $phpDocNode, PhpDocTagNode $phpDocTagNode): void
    {
        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            if ($phpDocChildNode === $phpDocTagNode) {
                unset($phpDocNode->children[$key]);
                return;
            }
        }
    }
}
