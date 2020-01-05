<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Tests\Finder;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\SetConfigResolver\ConfigResolver;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;

final class ConfigResolverTest extends TestCase
{
    /**
     * @var ConfigResolver
     */
    private $configResolver;

    protected function setUp(): void
    {
        $this->configResolver = new ConfigResolver();
    }

    /**
     * @dataProvider provideOptionsAndExpectedConfig()
     * @param mixed[] $options
     */
    public function testDetectFromInputAndProvideWithAbsolutePath(array $options, ?string $expectedConfig): void
    {
        $resolvedConfig = $this->configResolver->resolveFromInputWithFallback(new ArrayInput($options));
        $this->assertSame($expectedConfig, $resolvedConfig);
    }

    public function provideOptionsAndExpectedConfig(): Iterator
    {
        yield [['--config' => '.travis.yml'], getcwd() . '/.travis.yml'];
        yield [['-c' => '.travis.yml'], getcwd() . '/.travis.yml'];

        yield [['--config' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml'];
        yield [['-c' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml'];

        yield [['--', 'sh', '-c' => '/bin/true'], null];
    }

    public function testMissingFileInInput(): void
    {
        $this->expectException(FileNotFoundException::class);

        $input = new ArrayInput(['--config' => 'someFile.yml']);
        $this->configResolver->resolveFromInputWithFallback($input);
    }
}
