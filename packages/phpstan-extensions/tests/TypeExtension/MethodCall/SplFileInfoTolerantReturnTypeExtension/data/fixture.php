<?php

declare(strict_types=1);

use Symfony\Component\Finder\SplFileInfo;
use function PHPStan\Testing\assertType;

class SomeClass
{
    public function run(SplFileInfo $splFileInfo): void
    {
        $realPath = $splFileInfo->getRealPath();
        assertType('string', $realPath);
    }
}
