<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Tests\PHPUnit\Behavior\DirectoryAssertableTrait;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\PHPUnit\Behavior\DirectoryAssertableTrait;

final class DirectoryAssertableTraitTest extends TestCase
{
    use DirectoryAssertableTrait;

    public function testSuccess(): void
    {
        $this->assertDirectoryEquals(__DIR__ . '/Fixture/first_directory', __DIR__ . '/Fixture/second_directory');
    }

    public function testFail(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->assertDirectoryEquals(__DIR__ . '/Fixture/first_directory', __DIR__ . '/Fixture/third_directory');
    }
}
