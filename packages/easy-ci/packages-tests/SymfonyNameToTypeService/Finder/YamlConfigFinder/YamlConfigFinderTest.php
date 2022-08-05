<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\SymfonyNameToTypeService\Finder\YamlConfigFinder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCI\SymfonyNameToTypeService\Finder\YamlConfigFinder;

final class YamlConfigFinderTest extends TestCase
{
    private YamlConfigFinder $yamlConfigFinder;

    protected function setUp(): void
    {
        $this->yamlConfigFinder = new YamlConfigFinder();
    }

    public function test(): void
    {
        $yamlFileInfos = $this->yamlConfigFinder->findInDirectory(__DIR__ . '/Fixture');

        $this->assertCount(2, $yamlFileInfos);
        $this->assertContainsOnlyInstancesOf(SplFileInfo::class, $yamlFileInfos);
    }
}
