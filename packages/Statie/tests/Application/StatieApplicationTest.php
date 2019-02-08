<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Application;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\Exception\Utils\MissingDirectoryException;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Tests\AbstractConfigAwareContainerTestCase;

final class StatieApplicationTest extends AbstractConfigAwareContainerTestCase
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
        $this->statieApplication = $this->container->get(StatieApplication::class);
        $this->arrayLoader = $this->container->get(ArrayLoader::class);

        $symfonyStyle = $this->container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
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
        $this->assertNotEmpty($this->arrayLoader->getContent('layout/default.latte'));
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/StatieApplicationSource/statie.yml';
    }
}
