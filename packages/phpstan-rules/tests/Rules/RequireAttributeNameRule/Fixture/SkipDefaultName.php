<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\Fixture;

use Attribute;
#[Attribute(Attribute::TARGET_PROPERTY)]
final class SkipDefaultName
{

}
