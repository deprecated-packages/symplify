<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\Fixture;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;

final class IntersetionNode
{
    /**
     * @param Property|ClassMethod $node
     */
    public function process(\PhpParser\Node $node)
    {
        return $node->getAttribute('parent');
    }
}
