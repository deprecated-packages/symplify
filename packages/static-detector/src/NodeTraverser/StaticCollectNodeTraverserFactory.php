<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\NodeTraverser;

use PhpParser\NodeVisitor\NameResolver;
use Symplify\StaticDetector\NodeVisitor\FilePathNodeVisitor;
use Symplify\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;

final class StaticCollectNodeTraverserFactory
{
    public function __construct(
        private StaticCollectNodeVisitor $staticCollectNodeVisitor,
        private FilePathNodeVisitor $filePathNodeVisitor
    ) {
    }

    public function create(): StaticCollectNodeTraverser
    {
        $staticCollectNodeTraverser = new StaticCollectNodeTraverser();
        $staticCollectNodeTraverser->addVisitor(new NameResolver());
        $staticCollectNodeTraverser->addVisitor($this->staticCollectNodeVisitor);
        $staticCollectNodeTraverser->addVisitor($this->filePathNodeVisitor);

        return $staticCollectNodeTraverser;
    }
}
