<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNodeRule\Fixture;

final class SkipCommentIntentionally
{
    public function run()
    {
        // intentionally
        return @empty($value);
    }
}
