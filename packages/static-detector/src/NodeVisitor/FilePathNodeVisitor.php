<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symplify\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Symplify\StaticDetector\ValueObject\StaticDetectorAttributeKey;

final class FilePathNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;

    public function __construct(CurrentFileInfoProvider $currentFileInfoProvider)
    {
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }

    public function enterNode(Node $node)
    {
        $smartFileInfo = $this->currentFileInfoProvider->getSmartFileInfo();

        $fileLine = $smartFileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
        $node->setAttribute(StaticDetectorAttributeKey::FILE_LINE, $fileLine);

        return null;
    }
}
