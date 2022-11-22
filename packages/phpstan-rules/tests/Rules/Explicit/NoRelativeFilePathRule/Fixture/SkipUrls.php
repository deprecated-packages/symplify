<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipUrls
{
    public function run()
    {
        return 'https://someurl.com';
    }
}
