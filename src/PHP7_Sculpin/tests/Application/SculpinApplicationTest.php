<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Application;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Application\Command\RunCommand;
use Symplify\PHP7_Sculpin\Application\SculpinApplication;
use Symplify\PHP7_Sculpin\DI\Container\ContainerFactory;
use Symplify\PHP7_Sculpin\Renderable\Latte\DynamicStringLoader;

final class SculpinApplicationTest extends TestCase
{
    /**
     * @var SculpinApplication
     */
    private $sculpinApplication;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    protected function setUp()
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/SculpinApplicationSource/config/config.neon');
        $this->sculpinApplication = $container->getByType(SculpinApplication::class);
        $this->dynamicStringLoader = $container->getByType(DynamicStringLoader::class);
    }

    public function test()
    {
        $runCommand = new RunCommand(
            false,
            __DIR__ . '/SculpinApplicationSource/source',
            __DIR__ . '/SculpinApplicationSource/output'
        );
        $this->sculpinApplication->runCommand($runCommand);

        $this->assertFileExists(__DIR__ . '/SculpinApplicationSource/output/index.html');
        $this->assertFileEquals(
            __DIR__ . '/SculpinApplicationSource/expected-index.html',
            __DIR__ . '/SculpinApplicationSource/output/index.html'
        );

        $this->assertFileExists(__DIR__ . '/SculpinApplicationSource/output/feed.xml');
        $this->assertFileExists(__DIR__ . '/SculpinApplicationSource/output/atom.rss');

        $this->assertNotEmpty($this->dynamicStringLoader->getContent('default'));
    }

    /**
     * @expectedException \Symplify\PHP7_Sculpin\Exception\Utils\MissingDirectoryException
     */
    public function testRunForMissingSource()
    {
        $runCommand = new RunCommand(false, 'missing', 'random');
        $this->sculpinApplication->runCommand($runCommand);
    }

    protected function tearDown()
    {
        FileSystem::delete(__DIR__ . '/SculpinApplicationSource/output');
    }
}
