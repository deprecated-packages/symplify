<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Neon\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Neon\Loader\NeonLoader;

final class NeonLoaderTest extends TestCase
{
    /**
     * @var NeonLoader
     */
    private $neonLoader;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder;
        $this->neonLoader = new NeonLoader($this->containerBuilder);
    }

    public function test(): void
    {
        $this->neonLoader->load(__DIR__ . '/NeonLoaderSource/someConfig.neon');
        $this->assertSame('value', $this->containerBuilder->getParameter('key'));
    }
}
