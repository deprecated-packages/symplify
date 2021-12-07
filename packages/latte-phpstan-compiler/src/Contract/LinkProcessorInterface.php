<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Contract;

use PhpParser\Node\Stmt\Expression;

interface LinkProcessorInterface
{
    /**
     * checks if processor is available for target name
     */
    public function check(string $targetName): bool;

    /**
     * @return Expression[]
     */
    public function createLinkExpressions(string $targetName, array $linkParams, array $attributes): array;
}
