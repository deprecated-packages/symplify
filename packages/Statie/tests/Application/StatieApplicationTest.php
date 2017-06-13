<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Application;

use Nette\Utils\FileSystem;
use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\Exception\Utils\MissingDirectoryException;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class StatieApplicationTest extends AbstractContainerAwareTestCase
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
        $this->statieApplication = $this->container->get(StatieApplication::class);
        $this->dynamicStringLoader = $this->container->get(DynamicStringLoader::class);
    }

    public function test(): void
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

        $this->assertNotEmpty($this->dynamicStringLoader->getContent('default'));
    }

    public function testRunForMissingSource(): void
    {
        $this->expectException(MissingDirectoryException::class);
        $this->statieApplication->run('missing', 'random');
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/StatieApplicationSource/output');
    }
}
