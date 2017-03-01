<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Application;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\Statie\Application\Command\RunCommand;
use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\DI\Container\ContainerFactory;
use Symplify\Statie\Renderable\Latte\DynamicStringLoader;

final class StatieApplicationTest extends TestCase
{
    /**
     * @var StatieApplication
     */
    private $statieApplication;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../src/config/config.neon'
        );
        $this->statieApplication = $container->getByType(StatieApplication::class);
        $this->dynamicStringLoader = $container->getByType(DynamicStringLoader::class);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/StatieApplicationSource/output');
    }

    public function test(): void
    {
        $runCommand = new RunCommand(
            __DIR__ . '/StatieApplicationSource/source',
            __DIR__ . '/StatieApplicationSource/output'
        );
        $this->statieApplication->runCommand($runCommand);

        $this->assertFileExists(__DIR__ . '/StatieApplicationSource/output/index.html');
        $this->assertFileEquals(
            __DIR__ . '/StatieApplicationSource/expected-index.html',
            __DIR__ . '/StatieApplicationSource/output/index.html'
        );

        $this->assertFileExists(__DIR__ . '/StatieApplicationSource/output/feed.xml');
        $this->assertFileExists(__DIR__ . '/StatieApplicationSource/output/atom.rss');

        $this->assertNotEmpty($this->dynamicStringLoader->getContent('default'));
    }

    /**
     * @expectedException \Symplify\Statie\Exception\Utils\MissingDirectoryException
     */
    public function testRunForMissingSource(): void
    {
        $runCommand = new RunCommand('missing', 'random');
        $this->statieApplication->runCommand($runCommand);
    }
}
