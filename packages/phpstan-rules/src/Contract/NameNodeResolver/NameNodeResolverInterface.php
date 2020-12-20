<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract\NameNodeResolver;

use PhpParser\Node;

interface NameNodeResolverInterface
{
    public function match(Node $node): bool;

    public function resolve(Node $node): ?string;
}
