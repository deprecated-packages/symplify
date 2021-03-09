<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMagicClosureRule\Fixture;

$value = (static function () {
    // ...
});
