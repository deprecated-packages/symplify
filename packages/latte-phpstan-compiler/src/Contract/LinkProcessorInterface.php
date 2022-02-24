<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Contract;

use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\Expression;

interface LinkProcessorInterface
{
    /**
     * checks if processor is available for target name
     */
    public function check(string $targetName): bool;

    /**
     * @param Arg[] $linkParams
     * @param array<string, mixed> $attributes
     * @return Expression[]
     */
    public function createLinkExpressions(string $targetName, array $linkParams, array $attributes): array;
}
