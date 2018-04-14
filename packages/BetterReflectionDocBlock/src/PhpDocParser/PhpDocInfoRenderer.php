<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

final class PhpDocInfoRenderer
{
    public function render(PhpDocInfo $phpDocInfo): string
    {
        // @todo add format preserving renderer
        if ($phpDocInfo->isSingleLineDoc()) {
            return '/** ' . implode(" ", $phpDocInfo->getPhpDocNode()->children) . ' */';
        }

        return "/**\n * " . implode("\n * ", $phpDocInfo->getPhpDocNode()->children) . "\n */";
    }
}
