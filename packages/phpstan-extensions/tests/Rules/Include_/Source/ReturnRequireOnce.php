<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Include_\Source;

final class ReturnRequireOnce
{
    public function run()
    {
        return require_once 'Test.php';
    }
}
