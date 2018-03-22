<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Configuration;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;
use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;

final class ConfigFilePathHelperTest extends TestCase
{
    /**
     * @dataProvider provideOptionToValueWithExpectedPath()
     * @param mixed[] $options
     */
    public function testDetectFromInputAndProvideWithAbsolutePath(
        array $options,
        string $expectedConfig,
        string $message
    ): void {
        $name = md5(serialize($options));
        ConfigFilePathHelper::detectFromInput($name, new ArrayInput($options));

        $this->assertSame($expectedConfig, ConfigFilePathHelper::provide($name), $message);
    }

    public function provideOptionToValueWithExpectedPath(): Iterator
    {
        yield [['--config' => '.travis.yml'], getcwd() . '/.travis.yml', 'Full option with relative path'];
        yield [['-c' => '.travis.yml'], getcwd() . '/.travis.yml', 'Short option with relative path'];
        yield [['--config' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml', 'Full option with relative path'];
        yield [['-c' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml', 'Short option with relative path'];
    }

    public function testProvide(): void
    {
        $config = ConfigFilePathHelper::provide('some-value', '.travis.yml');
        $this->assertSame(getcwd() . '/.travis.yml', $config);
    }

    public function testMissingFileInInput(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(sprintf(
            'File "%s" not found in "%s"',
            getcwd() . '/someFile.yml',
            'someFile.yml'
        ));

        ConfigFilePathHelper::detectFromInput('name', new ArrayInput([
            '--config' => 'someFile.yml',
        ]));
    }
}
