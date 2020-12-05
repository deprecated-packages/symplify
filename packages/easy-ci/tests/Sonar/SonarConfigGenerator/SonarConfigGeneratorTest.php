<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Sonar\SonarConfigGenerator;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\Sonar\SonarConfigGenerator;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SonarConfigGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var SonarConfigGenerator
     */
    private $sonarConfigGenerator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->sonarConfigGenerator = $this->getService(SonarConfigGenerator::class);

        /** @var ParameterProvider $parameterProvider */
        $parameterProvider = $this->getService(ParameterProvider::class);
        $parameterProvider->changeParameter(Option::SONAR_ORGANIZATION, 'some_organization');
        $parameterProvider->changeParameter(Option::SONAR_PROJECT_KEY, 'some_project');
    }

    /**
     * @param array<string, mixed|mixed[]> $extraParameters
     * @dataProvider provideData()
     */
    public function test(array $extraParameters, string $expectedSonartConfig): void
    {
        $sonarConfigContent = $this->sonarConfigGenerator->generate([__DIR__ . '/Fixture'], $extraParameters);
        $this->assertStringMatchesFormatFile($expectedSonartConfig, $sonarConfigContent);
    }

    public function provideData(): Iterator
    {
        yield [[], __DIR__ . '/Fixture/expected_config.txt'];
        yield [[
            'sonar.extra' => 'extra_values',
        ], __DIR__ . '/Fixture/expected_modified_original_config.txt'];
    }
}
