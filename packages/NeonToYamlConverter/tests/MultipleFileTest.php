<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Tests;

use Symplify\NeonToYamlConverter\ArrayParameterCollector;
use Symplify\NeonToYamlConverter\HttpKernel\NeonToYamlConverterKernel;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class MultipleFileTest extends AbstractKernelTestCase
{
    /**
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    /**
     * @var SmartFileInfo
     */
    private $parametersFileInfo;

    /**
     * @var SmartFileInfo
     */
    private $servicesFileInfo;

    protected function setUp(): void
    {
        $this->bootKernel(NeonToYamlConverterKernel::class);
        $this->neonToYamlConverter = self::$container->get(NeonToYamlConverter::class);

        $arrayParameterCollector = self::$container->get(ArrayParameterCollector::class);

        $this->parametersFileInfo = new SmartFileInfo(__DIR__ . '/MultipleFileSource/neon/parameters.neon');
        $this->servicesFileInfo = new SmartFileInfo(__DIR__ . '/MultipleFileSource/neon/services.neon');

        $fileInfos = [$this->parametersFileInfo, $this->servicesFileInfo];
        $arrayParameterCollector->collectFromFiles($fileInfos);
    }

    public function test(): void
    {
        $convertedContent = $this->neonToYamlConverter->convertFile($this->parametersFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/parameters.yaml', $convertedContent);

        $convertedContent = $this->neonToYamlConverter->convertFile($this->servicesFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/services.yaml', $convertedContent);
    }

    public function testInversed(): void
    {
        $convertedContent = $this->neonToYamlConverter->convertFile($this->servicesFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/services.yaml', $convertedContent);

        $convertedContent = $this->neonToYamlConverter->convertFile($this->parametersFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/parameters.yaml', $convertedContent);
    }
}
