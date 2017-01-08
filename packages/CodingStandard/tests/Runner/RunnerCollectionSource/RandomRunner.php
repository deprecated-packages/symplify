<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Runner\RunnerCollectionSource;

use Symplify\CodingStandard\Contract\Runner\RunnerInterface;

final class RandomRunner implements RunnerInterface
{
    public function runForDirectory(string $directory) : string
    {
    }

    public function fixDirectory(string $directory) : string
    {
    }

    public function hasErrors() : bool
    {
    }
}
