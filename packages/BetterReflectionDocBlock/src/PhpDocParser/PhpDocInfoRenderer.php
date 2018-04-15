<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

final class PhpDocInfoRenderer
{
    public function render(PhpDocInfo $phpDocInfo): string
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        if ($phpDocInfo->isSingleLineDoc()) {
            return '/** ' . $phpDocNode->children[0] . ' */';
        }

        return "/**\n * " . implode("\n * ", $phpDocNode->children) . "\n */";
    }
}
