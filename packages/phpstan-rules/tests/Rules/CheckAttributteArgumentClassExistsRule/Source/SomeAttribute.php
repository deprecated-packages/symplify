<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\Source;

use Attribute;

#[Attribute]
final class SomeAttribute
{
    public function __construct(string $className)
    {
    }
}
