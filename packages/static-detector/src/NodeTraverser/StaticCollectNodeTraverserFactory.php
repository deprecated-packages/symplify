<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\NodeTraverser;

use PhpParser\NodeVisitor\NameResolver;
use Symplify\StaticDetector\NodeVisitor\FilePathNodeVisitor;
use Symplify\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;

final class StaticCollectNodeTraverserFactory
{
    /**
     * @var StaticCollectNodeVisitor
     */
    private $staticCollectNodeVisitor;

    /**
     * @var FilePathNodeVisitor
     */
    private $filePathNodeVisitor;

    public function __construct(
        StaticCollectNodeVisitor $staticCollectNodeVisitor,
        FilePathNodeVisitor $filePathNodeVisitor
    ) {
        $this->staticCollectNodeVisitor = $staticCollectNodeVisitor;
        $this->filePathNodeVisitor = $filePathNodeVisitor;
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
