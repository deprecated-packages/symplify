<?php

declare(strict_types=1);

namespace Symplify\EasyCI\StaticDetector\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey;

final class FilePathNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private CurrentFileInfoProvider $currentFileInfoProvider
    ) {
    }

    public function enterNode(Node $node)
    {
        $smartFileInfo = $this->currentFileInfoProvider->getSmartFileInfo();

        $fileLine = $smartFileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
        $node->setAttribute(StaticDetectorAttributeKey::FILE_LINE, $fileLine);

        return null;
    }
}
