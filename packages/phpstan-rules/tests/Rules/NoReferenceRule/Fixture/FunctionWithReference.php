<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Fixture;

function someMethod(&$smallBack)
{
}
