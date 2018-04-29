<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfo;

final class PhpDocModifier
{
    /**
     * @param string $tagName
     */
    public function removeTagByName(PhpDocInfo $phpDocInfo, string $tagName)
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        $tagsByName = $phpDocNode->getTagsByName('@' . ltrim($tagName, '@'));

        foreach ($tagsByName as $tagByName) {
            $this->removeTagFromPhpDocNode($phpDocNode, $tagByName);
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
