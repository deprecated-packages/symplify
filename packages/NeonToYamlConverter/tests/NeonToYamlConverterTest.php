<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Tests;

use Iterator;
use Symplify\NeonToYamlConverter\ArrayParameterCollector;
use Symplify\NeonToYamlConverter\HttpKernel\NeonToYamlConverterKernel;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class NeonToYamlConverterTest extends AbstractKernelTestCase
{
    /**
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    protected function setUp(): void
    {
        $this->bootKernel(NeonToYamlConverterKernel::class);

        $this->neonToYamlConverter = self::$container->get(NeonToYamlConverter::class);
        $this->arrayParameterCollector = self::$container->get(ArrayParameterCollector::class);
    }

    /**
     * @dataProvider provideData()
     * @dataProvider provideDataWithParameters()
     * @dataProvider provideDataWithServices()
     */
    public function test(string $inputFile, string $expectedFile): void
    {
        $inputFileInfo = new SmartFileInfo($inputFile);

        $this->arrayParameterCollector->collectFromFiles([$inputFileInfo]);

        $convertedFile = $this->neonToYamlConverter->convertFile($inputFileInfo);
        $this->assertStringEqualsFile($expectedFile, $convertedFile, 'Caused in file: ' . $inputFile);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/neon/lists.neon', __DIR__ . '/Source/yaml/lists.yaml'];
        yield [__DIR__ . '/Source/neon/multi_line_content.neon', __DIR__ . '/Source/yaml/multi_line_content.yaml'];
        yield [__DIR__ . '/Source/neon/entity.neon', __DIR__ . '/Source/yaml/entity.yaml'];
        yield [__DIR__ . '/Source/neon/imports.neon', __DIR__ . '/Source/yaml/imports.yaml'];
        yield [__DIR__ . '/Source/neon/env.neon', __DIR__ . '/Source/yaml/env.yaml'];
        yield [__DIR__ . '/Source/neon/tilda.neon', __DIR__ . '/Source/yaml/tilda.yaml'];
    }

    public function provideDataWithParameters(): Iterator
    {
        yield [
            __DIR__ . '/Source/neon/parameters/app_dir_parameter.neon',
            __DIR__ . '/Source/yaml/parameters/app_dir_parameter.yaml',
        ];

        yield [
            __DIR__ . '/Source/neon/parameters/skip_parameter_inline.neon',
            __DIR__ . '/Source/yaml/parameters/skip_parameter_inline.yaml',
        ];

        yield [
            __DIR__ . '/Source/neon/parameters/nested_parameter.neon',
            __DIR__ . '/Source/yaml/parameters/nested_parameter.yaml',
        ];
    }

    public function provideDataWithServices(): Iterator
    {
        yield [__DIR__ . '/Source/neon/services/basic.neon', __DIR__ . '/Source/yaml/services/basic.yaml'];
        yield [__DIR__ . '/Source/neon/services/null.neon', __DIR__ . '/Source/yaml/services/null.yaml'];
        yield [__DIR__ . '/Source/neon/services/list.neon', __DIR__ . '/Source/yaml/services/list.yaml'];
        yield [__DIR__ . '/Source/neon/services/alias.neon', __DIR__ . '/Source/yaml/services/alias.yaml'];
        yield [__DIR__ . '/Source/neon/services/entity.neon', __DIR__ . '/Source/yaml/services/entity.yaml'];
        yield [__DIR__ . '/Source/neon/services/setup.neon', __DIR__ . '/Source/yaml/services/setup.yaml'];

        yield [
            __DIR__ . '/Source/neon/services/without_namespace.neon',
            __DIR__ . '/Source/yaml/services/without_namespace.yaml',
        ];
        yield [__DIR__ . '/Source/neon/services/arguments.neon', __DIR__ . '/Source/yaml/services/arguments.yaml'];
    }
}
