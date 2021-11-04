<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Contract\Git;

interface TagResolverInterface
{
    public function resolve(string $gitDirectory): ?string;
}
