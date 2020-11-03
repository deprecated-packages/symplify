<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\Fixture;

use Nette\Utils\Strings;

final class SkipStringsSplit
{
    public function run()
    {
        [$one, $two] = Strings::split('SomeClass::SOME_CONSTANTS', '#::#');
    }
}
