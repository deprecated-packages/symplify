<?php

declare(strict_types=1);

namespace Symplify\EasyCI\StaticDetector\NodeTraverser;

use PhpParser\NodeVisitor\NameResolver;
use Symplify\EasyCI\StaticDetector\NodeVisitor\FilePathNodeVisitor;
use Symplify\EasyCI\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;

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
