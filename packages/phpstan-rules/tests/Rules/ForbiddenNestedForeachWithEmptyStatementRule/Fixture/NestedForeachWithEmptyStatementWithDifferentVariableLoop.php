<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule\Fixture;

foreach ($errors as $fileErrors) {
    // empty
    foreach ($foos as $foo) {
        echo $foo;
    }
}
