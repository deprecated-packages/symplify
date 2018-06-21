<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Application;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Exception\Utils\MissingDirectoryException;
use Symplify\Statie\Latte\Loader\ArrayLoader;

final class StatieApplicationTest extends TestCase
{
    /**
     * @var StatieApplication
     */
    private $statieApplication;

    /**
     * @var ArrayLoader
     */
    private $arrayLoader;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/StatieApplicationSource/statie.yml');
        $this->statieApplication = $container->get(StatieApplication::class);
        $this->arrayLoader = $container->get(ArrayLoader::class);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/StatieApplicationSource/output');
    }

    public function testRun(): void
    {
        $this->statieApplication->run(
            __DIR__ . '/StatieApplicationSource/source',
            __DIR__ . '/StatieApplicationSource/output'
        );

        $this->assertFileExists(__DIR__ . '/StatieApplicationSource/output/index.html');
        $this->assertFileEquals(
            __DIR__ . '/StatieApplicationSource/expected-index.html',
            __DIR__ . '/StatieApplicationSource/output/index.html'
        );

        $this->assertFileExists(__DIR__ . '/StatieApplicationSource/output/feed.xml');
        $this->assertFileExists(__DIR__ . '/StatieApplicationSource/output/atom.rss');

        $this->assertNotEmpty($this->arrayLoader->getContent('_layouts/default.latte'));
    }

    public function testRunForMissingSource(): void
    {
        $this->expectException(MissingDirectoryException::class);
        $this->statieApplication->run('missing', 'random');
    }

    public function testForSuggestedSource(): void
    {
        $this->statieApplication->run(
            __DIR__ . '/StatieApplicationSource/source',
            __DIR__ . '/StatieApplicationSource/output'
        );

        $this->expectExceptionMessageRegExp('#Did you mean "_layouts/default.latte"#');
        $this->assertNotEmpty($this->arrayLoader->getContent('layoutdefault.latte'));
    }
}
