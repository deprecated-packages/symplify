<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;

final class SeePhpDocTagNodesFinder
{
    /**
     * @return PhpDocTagNode[]
     */
    public function find(ResolvedPhpDocBlock $resolvedPhpDocBlock): array
    {
        $seePhpDocTagNodes = [];

        foreach ($resolvedPhpDocBlock->getPhpDocNodes() as $phpDocNode) {
            foreach ($phpDocNode->children as $phpDocChildNode) {
                if (! $phpDocChildNode instanceof PhpDocTagNode) {
                    continue;
                }

                if ($phpDocChildNode->name !== '@see') {
                    continue;
                }

                $seePhpDocTagNodes[] = $phpDocChildNode;
            }
        }

        return $seePhpDocTagNodes;
    }
}
