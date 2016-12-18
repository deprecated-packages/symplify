<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Runner\RunnerCollectionSource;

use Symplify\CodingStandard\Contract\Runner\RunnerInterface;

final class RandomRunner implements RunnerInterface
{
    /**
     * {@inheritdoc}
     */
    public function runForDirectory(string $directory) : string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fixDirectory(string $directory) : string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors() : bool
    {
    }
}
