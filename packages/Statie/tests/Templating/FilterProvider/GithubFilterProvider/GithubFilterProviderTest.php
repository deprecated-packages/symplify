<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Templating\FilterProvider\GithubFilterProvider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Templating\FilterProvider\GithubFilterProvider;

final class GithubFilterProviderTest extends TestCase
{
    /**
     * @var Container|ContainerInterface
     */
    private $container;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/GithubFilterProviderSource/statie-config-with-github-slug.yml'
        );

        $this->fileFactory = $this->container->get(FileFactory::class);

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/GithubFilterProviderSource/source');
    }

    public function test(): void
    {
        $githubFilterProvider = $this->container->get(GithubFilterProvider::class);
        $githubEditPostUrlFilter = $githubFilterProvider->provide()['githubEditPostUrl'];

        $this->assertSame(
            'https://github.com/TomasVotruba/tomasvotruba.cz/edit/master/source/_posts/2017-12-31-happy-new-years.md',
            $githubEditPostUrlFilter($this->getFile())
        );
    }

    private function getFile(): AbstractFile
    {
        $fileInfo = new SmartFileInfo(
            __DIR__ . '/GithubFilterProviderSource/source/_posts/2017-12-31-happy-new-years.md'
        );

        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
