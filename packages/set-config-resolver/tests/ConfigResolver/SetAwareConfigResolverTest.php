<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Tests\ConfigResolver;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\SetConfigResolver\Exception\SetNotFoundException;
use Symplify\SetConfigResolver\SetAwareConfigResolver;
use Symplify\SetConfigResolver\Tests\ConfigResolver\Source\DummySetProvider;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetAwareConfigResolverTest extends TestCase
{
    /**
     * @var SetAwareConfigResolver
     */
    private $setAwareConfigResolver;

    protected function setUp(): void
    {
        $this->setAwareConfigResolver = new SetAwareConfigResolver(new DummySetProvider());
    }

    /**
     * @dataProvider provideOptionsAndExpectedConfig()
     * @param mixed[] $options
     */
    public function testDetectFromInputAndProvideWithAbsolutePath(array $options, ?string $expectedConfig): void
    {
        $resolvedConfigFileInfo = $this->setAwareConfigResolver->resolveFromInput(new ArrayInput($options));

        if ($expectedConfig === null) {
            $this->assertNull($resolvedConfigFileInfo);
        } else {
            $this->assertSame($expectedConfig, $resolvedConfigFileInfo->getRealPath());
        }
    }

    public function provideOptionsAndExpectedConfig(): Iterator
    {
        yield [[
            '--config' => 'README.md',
        ], getcwd() . '/README.md'];
        yield [[
            '-c' => 'README.md',
        ], getcwd() . '/README.md'];

        yield [[
            '--config' => getcwd() . '/README.md',
        ], getcwd() . '/README.md'];
        yield [[
            '-c' => getcwd() . '/README.md',
        ], getcwd() . '/README.md'];

        yield [
            [
                '--',
                'sh',
                '-c' => '/bin/true',
            ],
            null,
        ];
    }

    public function testSetsNotFound(): void
    {
        $basicConfigFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/missing_set_config.yaml');

        $this->expectException(SetNotFoundException::class);
        $this->setAwareConfigResolver->resolveFromParameterSetsFromConfigFiles([$basicConfigFileInfo]);
    }

    public function testYamlSetsFileInfos(): void
    {
        $basicConfigFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/yaml_config_with_sets.yaml');
        $setFileInfos = $this->setAwareConfigResolver->resolveFromParameterSetsFromConfigFiles([$basicConfigFileInfo]);

        $this->assertCount(1, $setFileInfos);

        $setFileInfo = $setFileInfos[0];
        $expectedSetFileInfo = new SmartFileInfo(__DIR__ . '/Source/some_set.yaml');

        $this->assertEquals($expectedSetFileInfo, $setFileInfo);
    }

    public function testPhpSetsFileInfos(): void
    {
        $basicConfigFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/php_config_with_sets.php');
        $setFileInfos = $this->setAwareConfigResolver->resolveFromParameterSetsFromConfigFiles([$basicConfigFileInfo]);

        $this->assertCount(1, $setFileInfos);

        $setFileInfo = $setFileInfos[0];
        $expectedSetFileInfo = new SmartFileInfo(__DIR__ . '/Source/some_php_set.php');

        $this->assertEquals($expectedSetFileInfo, $setFileInfo);
    }

    public function testMissingFileInInput(): void
    {
        $this->expectException(FileNotFoundException::class);

        $arrayInput = new ArrayInput([
            '--config' => 'someFile.yml',
        ]);
        $this->setAwareConfigResolver->resolveFromInput($arrayInput);
    }
}
