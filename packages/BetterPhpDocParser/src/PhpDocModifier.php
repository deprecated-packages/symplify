<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfo;

final class PhpDocModifier
{
    public function removeTagByName(PhpDocInfo $phpDocInfo, string $tagName): void
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        $tagsByName = $phpDocNode->getTagsByName('@' . ltrim($tagName, '@'));

        foreach ($tagsByName as $tagByName) {
            $this->removeTagFromPhpDocNode($phpDocNode, $tagByName);
        }
    }

    public function removeTagByNameAndContent(PhpDocInfo $phpDocInfo, string $tagName, string $tagContent): void
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        $tagsByName = $phpDocNode->getTagsByName('@' . ltrim($tagName, '@'));

        foreach ($tagsByName as $phpDocTagNode) {
            if (! $phpDocTagNode instanceof PhpDocTagNode) {
                continue;
            }

            if ($phpDocTagNode->value instanceof PhpDocTagValueNode) {
                $valueContent = (string) $phpDocTagNode->value;
                if ($valueContent === $tagContent) {
                    $this->removeTagFromPhpDocNode($phpDocNode, $phpDocTagNode);
                }
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
