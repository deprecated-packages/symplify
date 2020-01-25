<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Include_\Source;

final class AssignRequireOnce
{
    public function run()
    {
        $result = require_once 'Test.php';
    }
}
