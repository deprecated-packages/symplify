<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoMissingDirPathRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipAssertMethod extends TestCase
{
    public function test()
    {
        $this->assertFileExists(__DIR__ . '/../PotentialFile.php');
        $this->assertFileNotExists(__DIR__ . '/../../PotentialFile.php');

        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/temp/file.php');
        } else {
            $this->assertFileNotExists(__DIR__ . '/temp/file.php');
        }

        $possibleFile = __DIR__ . '/Whatever';
    }
}
