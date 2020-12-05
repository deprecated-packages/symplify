<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Tests;

use Symplify\NeonToYamlConverter\ArrayParameterCollector;
use Symplify\NeonToYamlConverter\HttpKernel\NeonToYamlKernel;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

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
        $this->bootKernel(NeonToYamlKernel::class);
        $this->neonToYamlConverter = $this->getService(NeonToYamlConverter::class);

        $arrayParameterCollector = $this->getService(ArrayParameterCollector::class);

        $this->parametersFileInfo = new SmartFileInfo(__DIR__ . '/MultipleFileSource/neon/parameters.neon');
        $this->servicesFileInfo = new SmartFileInfo(__DIR__ . '/MultipleFileSource/neon/services.neon');

        $fileInfos = [$this->parametersFileInfo, $this->servicesFileInfo];
        $arrayParameterCollector->collectFromFiles($fileInfos);
    }

    public function test(): void
    {
        $convertedContent = $this->neonToYamlConverter->convertFileInfo($this->parametersFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/parameters.yaml', $convertedContent);

        $convertedContent = $this->neonToYamlConverter->convertFileInfo($this->servicesFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/services.yaml', $convertedContent);
    }

    public function testInversed(): void
    {
        $convertedContent = $this->neonToYamlConverter->convertFileInfo($this->servicesFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/services.yaml', $convertedContent);

        $convertedContent = $this->neonToYamlConverter->convertFileInfo($this->parametersFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/MultipleFileSource/yaml/parameters.yaml', $convertedContent);
    }
}
