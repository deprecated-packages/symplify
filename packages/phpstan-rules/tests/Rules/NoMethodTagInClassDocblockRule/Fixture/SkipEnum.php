<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMethodTagInClassDocblockRule\Fixture;

use MyCLabs\Enum\Enum;

/**
 * @method getMagic() string
 */
final class SkipEnum extends Enum
{
}
