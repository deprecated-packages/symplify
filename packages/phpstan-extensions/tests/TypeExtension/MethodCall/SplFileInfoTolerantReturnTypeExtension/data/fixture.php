<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;
use Symfony\Component\Finder\SplFileInfo;

class SomeClass
{
    public function run(SplFileInfo $splFileInfo)
    {
        $realPath = $splFileInfo->getRealPath();
        assertType('string', $realPath);
    }
}
