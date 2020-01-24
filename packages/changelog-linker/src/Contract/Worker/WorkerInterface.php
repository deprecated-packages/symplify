<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Contract\Worker;

interface WorkerInterface
{
    public function processContent(string $content): string;

    /**
     * Higher priority goes first.
     */
    public function getPriority(): int;
}
