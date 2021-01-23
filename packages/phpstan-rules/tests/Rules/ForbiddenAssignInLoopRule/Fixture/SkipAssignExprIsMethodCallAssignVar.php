<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignExprIsMethodCallAssignVar
{
    public function run()
    {
        foreach ($this->postRectors as $postRector) {
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor($postRector);
            $nodes = $nodeTraverser->traverse($nodes);
        }
    }
}