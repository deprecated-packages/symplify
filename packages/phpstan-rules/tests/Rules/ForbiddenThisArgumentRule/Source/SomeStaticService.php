<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Source;

final class SomeStaticService
{
    public static function someMethod($value)
    {
    }
}
