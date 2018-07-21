<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Yaml\ParametersMergingYamlLoader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Yaml\ParametersMergingYamlLoader;

final class ParametersMergingYamlLoaderTest extends TestCase
{
    /**
     * @var ParametersMergingYamlLoader
     */
    private $parametersMergingYamlLoader;

    protected function setUp(): void
    {
        $this->parametersMergingYamlLoader = new ParametersMergingYamlLoader();
    }

    public function test(): void
    {
        $parametersBag = $this->parametersMergingYamlLoader->loadParameterBagFromFile(
            __DIR__ . '/Source/config.yml'
        );

        $this->assertInstanceOf(ParameterBagInterface::class, $parametersBag);

        $this->assertTrue($parametersBag->has('festivals'));
        $this->assertCount(3, $parametersBag->get('festivals'));
        $this->assertSame(['one', 'two', 'three'], $parametersBag->get('festivals'));
    }
}
