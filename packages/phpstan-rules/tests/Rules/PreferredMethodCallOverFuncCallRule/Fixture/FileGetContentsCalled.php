<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverFuncCallRule\Fixture;

final class FileGetContentsCalled
{
    public function run()
    {
        return file_get_contents('foo.txt');
    }
}
