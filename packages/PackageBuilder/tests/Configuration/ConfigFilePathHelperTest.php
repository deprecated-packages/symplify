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
        string $name,
        array $options,
        string $expectedConfig
    ): void {
        ConfigFilePathHelper::detectFromInput($name, new ArrayInput($options));

        $this->assertSame($expectedConfig, ConfigFilePathHelper::provide($name));
    }

    public function provideOptionToValueWithExpectedPath(): Iterator
    {
        # relative path
        yield ['name-1', ['--config' => '.travis.yml'], getcwd() . '/.travis.yml'];
        yield ['name-2', ['-c' => '.travis.yml'], getcwd() . '/.travis.yml'];

        # absolute path
        yield ['name-3', ['--config' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml'];
        yield ['name-4', ['-c' => getcwd() . '/.travis.yml'], getcwd() . '/.travis.yml'];
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
