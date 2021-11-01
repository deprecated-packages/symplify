<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\YamlToPhpConverter;

use Symplify\ConfigTransformer\Converter\YamlToPhpConverter;
use Symplify\ConfigTransformer\Kernel\ConfigTransformerKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class YamlToPhpConverterTest extends AbstractKernelTestCase
{
    private YamlToPhpConverter $yamlToPhpConverter;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigTransformerKernel::class);
        $this->yamlToPhpConverter = $this->getService(YamlToPhpConverter::class);
    }

    public function test(): void
    {
        $printedPhpConfigContent = $this->yamlToPhpConverter->convertYamlArray([
            'parameters' => [
                'key' => 'value',
            ],
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_parameters.php', $printedPhpConfigContent);
    }
}
