<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Configuration;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;

final class ConfigFileFinderTest extends TestCase
{
    /**
     * @dataProvider provideOptionsAndExpectedConfig()
     * @param mixed[] $options
     */
    public function testDetectFromInputAndProvideWithAbsolutePath(
        array $options,
        ?string $expectedConfig,
        string $message
    ): void {
        $name = md5(serialize($options));
        ConfigFileFinder::detectFromInput($name, new ArrayInput($options));

        $this->assertSame($expectedConfig, ConfigFileFinder::provide($name), $message);
    }

    public function provideOptionsAndExpectedConfig(): Iterator
    {
        yield [['--config' => '.travis.yml'], getcwd() . '/.travis.yml', 'Full option with relative path'];
        yield [['-c' => '.travis.yml'], getcwd() . '/.travis.yml', 'Short option with relative path'];
        yield [['--config' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml', 'Full option with relative path'];
        yield [['-c' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml', 'Short option with relative path'];
        yield [['--', 'sh', '-c' => '/bin/true'], null, 'Skip parameters following an end of options (--) signal'];
    }

    public function testProvide(): void
    {
        $config = ConfigFileFinder::provide('some-value', ['.travis.yml']);
        $this->assertSame(getcwd() . '/.travis.yml', $config);

        $config = ConfigFileFinder::provide('some-value', ['.travis.yaml', '.travis.yml']);
        $this->assertSame(getcwd() . '/.travis.yml', $config);
    }

    public function testMissingFileInInput(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf('File "%s" not found in "%s"', getcwd() . '/someFile.yml', 'someFile.yml')
        );

        ConfigFileFinder::detectFromInput('name', new ArrayInput(['--config' => 'someFile.yml']));
    }
}
