<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipNoFileBefore
{
    public function hide()
    {
        return '.txt';
    }

    public function seek()
    {
        return '*.txt';
    }
}
