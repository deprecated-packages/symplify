<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAttributteArgumentRule\Source;

use Attribute;

#[Attribute]
final class AnotherAttribute
{
    public function __construct(string $forbiddenKey = null)
    {
    }
}
