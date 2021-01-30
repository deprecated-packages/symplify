<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Tests\DataProvider\StaticFixtureFinder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class StaticFixtureFinderTest extends TestCase
{
    public function testYieldDirectory(): void
    {
        $files = StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.php');
        $this->assertCount(1, iterator_to_array($files));
    }

    public function testYieldDirectoryThrowException(): void
    {
        $this->expectException(ShouldNotHappenException::class);
        $this->expectExceptionMessage('"foo.txt" has invalid suffix, use "*.php" suffix instead');
        $files = StaticFixtureFinder::yieldDirectory(__DIR__ . '/FixtureMulti', '*.php');
        iterator_to_array($files);
    }

    public function testYieldDirectoryExclusively(): void
    {
        $files = StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/FixtureMulti', '*.php');
        $this->assertCount(1, iterator_to_array($files));
    }
}
