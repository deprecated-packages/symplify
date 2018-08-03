<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Yaml\ParameterMergingYamlLoader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Yaml\ParameterMergingYamlLoader;

final class ParameterMergingYamlLoaderTest extends TestCase
{
    /**
     * @var ParameterMergingYamlLoader
     */
    private $parameterMergingYamlLoader;

    protected function setUp(): void
    {
        $this->parameterMergingYamlLoader = new ParameterMergingYamlLoader();
    }

    public function test(): void
    {
        $parametersBag = $this->parameterMergingYamlLoader->loadParameterBagFromFile(
            __DIR__ . '/Source/config.yml'
        );

        $this->assertInstanceOf(ParameterBagInterface::class, $parametersBag);

        $this->assertTrue($parametersBag->has('festivals'));
        $this->assertCount(3, $parametersBag->get('festivals'));
        $this->assertSame(['one', 'two', 'three'], $parametersBag->get('festivals'));
    }
}
