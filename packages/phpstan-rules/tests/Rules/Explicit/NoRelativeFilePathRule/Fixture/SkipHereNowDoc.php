<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipHereNowDoc
{
    public function run()
    {
        return <<<'CODE_SAMPLE'
(static function () {
    // ...
})
CODE_SAMPLE;
    }
}
