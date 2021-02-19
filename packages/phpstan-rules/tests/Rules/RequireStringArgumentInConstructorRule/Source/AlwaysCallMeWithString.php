<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Source;

final class AlwaysCallMeWithString
{
    public function __construct($object, $type)
    {
    }
}
