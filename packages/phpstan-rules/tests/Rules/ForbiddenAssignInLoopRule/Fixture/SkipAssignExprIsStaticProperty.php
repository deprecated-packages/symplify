<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignExprIsStaticProperty
{
    public function run()
    {
        foreach ($methods as $method) {
            if (! $this->nodeNameResolver->isName($node->name, $method)) {
                return false;
            }

            $node2 = $node::$var;
        }
    }
}