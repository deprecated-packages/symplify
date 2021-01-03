<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMethodTagInClassDocblockRule\Fixture;

/**
 * @property int $id
 */
class SkipClassWithNoMethodTag
{
    protected $id;
}
