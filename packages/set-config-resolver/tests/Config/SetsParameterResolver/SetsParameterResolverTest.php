<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Tests\Config\SetsParameterResolver;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\SetConfigResolver\Config\SetsParameterResolver;

final class SetsParameterResolverTest extends TestCase
{
    /**
     * @var SetsParameterResolver
     */
    private $setsParameterResolver;

    protected function setUp(): void
    {
        $this->setsParameterResolver = new SetsParameterResolver();
    }

    /**
     * @dataProvider provideTests()
     */
    public function test(string $configFile, array $expectedSets): void
    {
        $resolvedSets = $this->setsParameterResolver->resolveFromConfigFiles([$configFile]);
        $this->assertSame($expectedSets, $resolvedSets);
    }

    public function provideTests(): Iterator
    {
        yield [__DIR__ . '/Fixture/sets.yaml', ['first_set', 'second_set']];
        yield [__DIR__ . '/Fixture/sets.php', ['old-set']];
        yield [__DIR__ . '/Fixture/sets_mixed_with_atypical_yaml.yaml', ['atypical']];
    }
}
